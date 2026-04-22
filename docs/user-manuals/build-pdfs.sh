#!/usr/bin/env bash
set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUT="${DIR}/output"
mkdir -p "${OUT}"
cd "$DIR"

PANDOC_DOCKER_IMAGE="${PANDOC_DOCKER_IMAGE:-pandoc/extra:3.1.1.0}"

detect_pdf_engine() {
  if command -v xelatex >/dev/null 2>&1; then
    echo "xelatex"
    return
  fi
  if command -v tectonic >/dev/null 2>&1; then
    echo "tectonic"
    return
  fi
  echo ""
}

PDF_ENGINE="$(detect_pdf_engine)"

# Prefer LuaLaTeX for Arabic (HarfBuzz shaping → proper letter joining) when installed.
engine_for_source() {
  local src="$1"
  case "${src}" in
    *.ar.md)
      if command -v lualatex >/dev/null 2>&1; then
        echo lualatex
        return
      fi
      ;;
  esac
  echo "${PDF_ENGINE}"
}

pandoc_common_args=(
  --from=markdown+yaml_metadata_block
  -V "documentclass=article"
  -V "fontsize=11pt"
  -V "papersize=a4"
  -V "geometry:margin=2.2cm"
  --toc
  --toc-depth=2
  -N
)

# Arabic PDFs: bundled Amiri under fonts/ + bidi; custom template skips unicode-math and loads preamble after hyperref.
arabic_pdf_extra_args=(
  --template="${DIR}/pandoc-template-arabic.latex"
  -M "arabic-pdf=true"
  -H "${DIR}/arabic-preamble.tex"
)

arabic_pdf_extra_args_docker=(
  --template=/data/pandoc-template-arabic.latex
  -M "arabic-pdf=true"
  -H /data/arabic-preamble.tex
)

run_pandoc_pdf() {
  local engine="$1"
  local src="$2"
  local dest="$3"
  local -a cmd=(
    pandoc "${DIR}/${src}" -o "${OUT}/${dest}"
    --pdf-engine="${engine}"
    "${pandoc_common_args[@]}"
  )

  case "${src}" in
    *.ar.md)
      if [[ ! -f "${DIR}/fonts/Amiri-Regular.ttf" || ! -f "${DIR}/fonts/Amiri-Bold.ttf" ]]; then
        echo "ERROR: Missing bundled Amiri fonts in ${DIR}/fonts/ (Amiri-Regular.ttf, Amiri-Bold.ttf)." >&2
        exit 1
      fi
      cmd+=("${arabic_pdf_extra_args[@]}")
      ;;
  esac

  "${cmd[@]}"
}

build_one() {
  local src="$1"
  local dest="$2"
  local engine="$3"

  case "${engine}" in
    lualatex|xelatex|tectonic)
      run_pandoc_pdf "${engine}" "${src}" "${dest}"
      ;;
    *)
      echo "ERROR: No PDF engine found. Install one of: lualatex (best for Arabic), xelatex (MacTeX/BasicTeX), tectonic (brew install tectonic), or Docker for pandoc/extra." >&2
      exit 1
      ;;
  esac
}

build_one_docker() {
  local src="$1"
  local dest="$2"
  local pdf_engine="xelatex"
  case "${src}" in
    *.ar.md) pdf_engine="lualatex" ;;
  esac

  local -a cmd=(
    docker run --rm
    -v "${DIR}:/data"
    -w /data
    "${PANDOC_DOCKER_IMAGE}"
    "${src}"
    -o "output/${dest}"
    --pdf-engine="${pdf_engine}"
    "${pandoc_common_args[@]}"
  )

  case "${src}" in
    *.ar.md)
      if [[ ! -f "${DIR}/fonts/Amiri-Regular.ttf" || ! -f "${DIR}/fonts/Amiri-Bold.ttf" ]]; then
        echo "ERROR: Missing bundled Amiri fonts in ${DIR}/fonts/." >&2
        exit 1
      fi
      cmd+=("${arabic_pdf_extra_args_docker[@]}")
      ;;
  esac

  "${cmd[@]}"
}

run_build() {
  local src="$1"
  local dest="$2"
  local eng
  eng="$(engine_for_source "${src}")"

  if [[ -n "${eng}" ]]; then
    echo "Using PDF engine '${eng}' for ${dest}..."
    build_one "${src}" "${dest}" "${eng}"
    return
  fi

  if command -v docker >/dev/null 2>&1; then
    echo "Using Docker (${PANDOC_DOCKER_IMAGE}) for ${dest}..."
    docker pull "${PANDOC_DOCKER_IMAGE}" >/dev/null
    build_one_docker "${src}" "${dest}"
    return
  fi

  echo "ERROR: No pandoc PDF engine and no docker. See README.md." >&2
  exit 1
}

if ! command -v pandoc >/dev/null 2>&1; then
  echo "ERROR: pandoc is required (brew install pandoc)." >&2
  exit 1
fi

run_build "admin.en.md" "BizBuradayiz-Admin-Manual-EN.pdf"
run_build "admin.tr.md" "BizBuradayiz-Admin-Manual-TR.pdf"
run_build "admin.ar.md" "BizBuradayiz-Admin-Manual-AR.pdf"
run_build "provider.en.md" "BizBuradayiz-Provider-Guide-EN.pdf"
run_build "provider.tr.md" "BizBuradayiz-Provider-Guide-TR.pdf"
run_build "provider.ar.md" "BizBuradayiz-Provider-Guide-AR.pdf"

rm -f "${OUT}/test-tectonic.pdf" "${OUT}/test-tr.pdf" 2>/dev/null || true

echo "Done. PDFs are in: ${OUT}/"
