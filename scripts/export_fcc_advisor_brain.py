#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
from pathlib import Path
import subprocess


def extract_bracketed(code: str, start_index: int, open_char: str = "[", close_char: str = "]") -> str:
    depth = 0
    in_single = False
    in_double = False
    escaped = False

    for index in range(start_index, len(code)):
        char = code[index]

        if in_single:
            if not escaped and char == "\\":
                escaped = True
                continue
            if not escaped and char == "'":
                in_single = False
            escaped = False
            continue

        if in_double:
            if not escaped and char == "\\":
                escaped = True
                continue
            if not escaped and char == '"':
                in_double = False
            escaped = False
            continue

        if char == "'":
            in_single = True
            continue

        if char == '"':
            in_double = True
            continue

        if char == open_char:
            depth += 1
        elif char == close_char:
            depth -= 1
            if depth == 0:
                return code[start_index:index + 1]

    raise RuntimeError("Failed to extract bracketed block.")


def extract_function_block(code: str, signature: str) -> str:
    start = code.find(signature)
    if start == -1:
        raise RuntimeError(f"Could not find function signature: {signature}")

    brace_start = code.find("{", start)
    if brace_start == -1:
        raise RuntimeError(f"Could not find opening brace for: {signature}")

    return extract_bracketed(code, brace_start, "{", "}")


def extract_return_array(function_block: str, occurrence: int = 1) -> str:
    marker = "return ["
    start = -1

    for _ in range(occurrence):
        start = function_block.find(marker, start + 1)
        if start == -1:
            raise RuntimeError(f"Could not find return array occurrence #{occurrence}")

    bracket_start = function_block.find("[", start)
    if bracket_start == -1:
        raise RuntimeError("Could not locate return array bracket.")

    return extract_bracketed(function_block, bracket_start)


def extract_variable_array(function_block: str, variable_name: str) -> str:
    marker = f"${variable_name} = ["
    start = function_block.find(marker)
    if start == -1:
        raise RuntimeError(f"Could not find variable array: {variable_name}")

    bracket_start = function_block.find("[", start)
    if bracket_start == -1:
        raise RuntimeError(f"Could not locate variable array bracket: {variable_name}")

    return extract_bracketed(function_block, bracket_start)


def decode_php_array(array_code: str, container_name: str = "radna-verzija-app"):
    php_code = (
        "$code = stream_get_contents(STDIN);"
        "$data = eval('return ' . $code . ';');"
        "echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);"
    )

    result = subprocess.run(
        ["docker", "exec", "-i", container_name, "php", "-r", php_code],
        input=array_code,
        text=True,
        capture_output=True,
        check=False,
    )

    if result.returncode != 0:
        raise RuntimeError(result.stderr.strip() or "Failed to evaluate PHP array via Docker.")

    return json.loads(result.stdout)


def main() -> int:
    parser = argparse.ArgumentParser(description="Export FCC advisor brain snapshots into AVC.")
    parser.add_argument(
        "--source",
        default="/Users/stjepanbelosa/Documents/product/app/helpers/fcc_ai.php",
        help="Path to FCC helper source file.",
    )
    parser.add_argument(
        "--output-dir",
        default="platform/data/advisor",
        help="Directory where exported JSON snapshots should be stored.",
    )
    args = parser.parse_args()

    source_path = Path(args.source)
    if not source_path.is_file():
        raise SystemExit(f"FCC helper source not found: {source_path}")

    output_dir = Path(args.output_dir)
    output_dir.mkdir(parents=True, exist_ok=True)

    source = source_path.read_text(encoding="utf-8")

    matrix_function = extract_function_block(source, "function fcc_ai_get_product_advisor_recommendation_matrix(): array")
    theme_function = extract_function_block(source, "function fcc_ai_get_public_recommendation_theme_catalog(string $assistant_type): array")
    direct_lookup_function = extract_function_block(source, "function fcc_ai_get_public_direct_product_lookup_matches(string $message): array")

    matrix = decode_php_array(extract_return_array(matrix_function))
    themes = decode_php_array(extract_return_array(theme_function, occurrence=2))
    direct_lookup_catalog = decode_php_array(extract_variable_array(direct_lookup_function, "catalog"))

    outputs = {
        "fcc_product_advisor_matrix.json": matrix,
        "fcc_product_advisor_theme_catalog.json": themes,
        "fcc_direct_product_lookup_catalog.json": direct_lookup_catalog,
    }

    for filename, payload in outputs.items():
        target = output_dir / filename
        target.write_text(
            json.dumps(payload, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )

    manifest = {
        "source_path": str(source_path),
        "exported_files": sorted(outputs.keys()),
    }
    (output_dir / "fcc_brain_manifest.json").write_text(
        json.dumps(manifest, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )

    print(f"Exported FCC advisor brain snapshots to {output_dir}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
