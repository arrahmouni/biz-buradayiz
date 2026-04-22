# Biz Buradayiz — user manuals (source)

This folder contains **Markdown sources** for six end-user PDF manuals (EN / TR / AR):

| Output file (in `output/`) | Source | Audience |
|----------------------------|--------|----------|
| `BizBuradayiz-Admin-Manual-EN.pdf` | `admin.en.md` | Administrators |
| `BizBuradayiz-Admin-Manual-TR.pdf` | `admin.tr.md` | Yöneticiler |
| `BizBuradayiz-Admin-Manual-AR.pdf` | `admin.ar.md` | المشرفون |
| `BizBuradayiz-Provider-Guide-EN.pdf` | `provider.en.md` | Service providers |
| `BizBuradayiz-Provider-Guide-TR.pdf` | `provider.tr.md` | Hizmet sağlayıcıları |
| `BizBuradayiz-Provider-Guide-AR.pdf` | `provider.ar.md` | مقدمو الخدمة |

**Arabic PDFs** use a small custom LaTeX template ([`pandoc-template-arabic.latex`](pandoc-template-arabic.latex)) so **Amiri** (bundled under [`fonts/`](fonts/)) loads correctly: the usual Pandoc settings for Arabic put invalid language options on the article class, and the default **unicode-math** stack interferes with Arabic text. The preamble [`arabic-preamble.tex`](arabic-preamble.tex) loads Amiri with **`Script=Arabic`** (required for **cursive joining** — do not use `Ligatures=TeX` on the Arabic face). With **LuaLaTeX**, it uses **polyglossia** + **Renderer=Harfbuzz**; with **XeTeX/tectonic**, it uses **bidi** after **hyperref**. The build script prefers **`lualatex`** for `*.ar.md` when installed (best shaping). Otherwise install [BasicTeX](https://www.tug.org/mactex/morepackages.html) and ensure `lualatex` is on `PATH`, or use Docker (the script uses **`lualatex`** for Arabic PDFs inside the image). Run the build script from anywhere — it `cd`s into this folder so `Path=fonts/` resolves reliably.

## Build PDFs

From this directory:

```bash
./build-pdfs.sh
```

The script uses Pandoc with a PDF engine in this order:

1. **`xelatex`** if installed (full MacTeX/BasicTeX).
2. **`tectonic`** if installed — lightweight, good default on macOS:

   ```bash
   brew install pandoc tectonic
   ```

3. **Docker** image `pandoc/extra` if no local engine is found (LaTeX included).

### Optional: `xelatex` (macOS)

```bash
brew install pandoc basictex
```

After BasicTeX, you may need extra packages for some languages:

```bash
sudo tlmgr update --self
sudo tlmgr install xetex polyglossia fontspec euenc xunicode
```

### Docker only

Ensure Docker is running; the script will pull `pandoc/extra` if missing.

## Regenerating after edits

Edit the `.md` files, then run `./build-pdfs.sh` again. PDFs are written to `output/`.
