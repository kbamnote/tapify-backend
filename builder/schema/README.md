# Builder Schema — the contract

Everything in the no-code builder is driven by the files in this folder. Nothing here
is Tapify-specific plumbing; it is the **shared contract** between four consumers:

| Consumer | Uses the schema to… |
|---|---|
| **Renderer** (Next.js) | know which component + variant to render, and with what props |
| **Web builder** | auto-generate the Inspector form for the selected section |
| **Mobile builder** (React Native) | auto-generate the same form with native widgets |
| **AI generator** (later) | emit a document that is valid by construction |

> **Rule:** if you find yourself writing section-specific code in an editor, stop.
> The editors must stay generic. Section knowledge lives *only* in these manifests.

---

## Files

```
schema/
  site.schema.json        JSON Schema for the whole Site Document (validated on every save)
  sections/<type>.json    one manifest per section type   ← the section library
  themes/<preset>.json    theme token presets
  industries/<id>.json    industry -> recommended sections (also drives AI)
```

---

## Layout model: stacked sections

A page is an **ordered array of full-width blocks**. Array order *is* visual order.
There are deliberately **no x/y coordinates** — this is what keeps every site
responsive automatically and stops customers from breaking their own layouts.
Customers choose *which* blocks, in *what order*, and edit what's *inside* them.

---

## Adding a new section (the whole process)

1. Add `sections/<type>.json` (this folder).
2. Add the matching renderer component.
3. Done — both editors pick it up automatically. No editor code, no app release.

---

## Manifest format

```jsonc
{
  "type": "hero",            // unique id, matches section.type + the component name
  "label": "Hero",           // shown in the Add-Section library
  "category": "header",      // header | content | social-proof | media | commerce | contact | footer
  "icon": "layout-template", // lucide icon name
  "industries": ["*"],       // ["*"] = all, or ["gym","coaching"] to scope suggestions
  "singleton": false,        // true = only one allowed per site (e.g. footer)
  "maxPerPage": 2,

  "variants": [ { "id": "split", "label": "Text left, image right", "preview": "..." } ],

  "props":  [ /* field definitions — see registry below */ ],

  "style":  {
    "supports": ["paddingY","align","bg","overlay"],   // which sectionStyle keys apply
    "defaults": { "paddingY": "xl" }
  },

  "defaults": { "variant": "split", "props": { "heading": "..." } }  // used when inserted
}
```

### Field definition (shared keys)

| Key | Meaning |
|---|---|
| `key` | property name written into `section.props` |
| `type` | one of the field types below |
| `label` | shown in the editor |
| `group` | groups fields into Inspector tabs/accordions ("Content", "Buttons"…) |
| `required` | validation |
| `default` | value used when the section is inserted |
| `help` | hint text under the field |
| `placeholder`, `maxLength`, `min`, `max` | validation / UX |
| `showIf` | conditional visibility, e.g. `{ "variant": ["split"] }` |

---

## Field type registry

**Both editors must implement exactly these types.** Adding a *new type* is the only
change that requires touching editor code — so add types rarely and deliberately.

| `type` | Web widget | Mobile widget | Value stored |
|---|---|---|---|
| `text` | single-line input | `TextInput` | `string` |
| `textarea` | multi-line | `TextInput multiline` | `string` |
| `richtext` | small WYSIWYG (bold/italic/link only) | limited editor | sanitized HTML subset |
| `number` | number input | numeric keyboard | `number` |
| `toggle` | switch | `Switch` | `boolean` |
| `select` | dropdown (`options`) | picker | `string` |
| `color` | colour picker | colour picker | hex string |
| `media` | media library picker (`accept`) | picker + camera/gallery | `"media:<id>"` |
| `icon` | icon picker | icon picker | icon name |
| `link` | text + url + newTab + style | grouped fields | `{ text, href, newTab, style }` |
| `repeater` | sortable list of `fields` | sortable list | `array<object>` |
| `list` | simple sortable strings | sortable list | `array<string>` |

### `repeater` extras
`fields` (the per-item field definitions), `min`, `max`, `itemLabel`
(template like `"{{title}}"` used for the collapsed row), `addLabel`.

---

## Validation happens in two passes

1. **Document shape** → `site.schema.json` (pages, sections, theme, ids, slugs…).
   `section.props` is intentionally a free-form object here.
2. **Section props** → the section's own manifest.

This two-pass design is what lets the section library grow to hundreds of types
**without ever touching `site.schema.json`**.

Both passes run **server-side on every save** — never trust the client, and this is
also what makes future AI output safe (invalid docs are rejected, not rendered).

---

## Media

Always store `"media:<id>"`, never a raw URL. The renderer resolves the id to a URL
(and to responsive derivatives). This is why a file can be replaced, renamed or moved
to a CDN without rewriting a single document.
