# AI Growth Center — backend module

A modular, **provider-independent** AI layer for the Tapify app. Business logic
depends only on interfaces, so Gemini / OpenAI / Claude / OpenRouter are
interchangeable with a single env var — no code changes.

## 1. Setup

**a) Run the migration** (creates `ai_cache` + `ai_history`):

```sql
SOURCE migration_ai_growth.sql;
```

**b) Set environment variables** (never hardcode keys). Defaults live in
`config/database.php`:

| Env var           | Default              | Notes                              |
|-------------------|----------------------|------------------------------------|
| `AI_PROVIDER`     | `gemini`             | `gemini` \| `openai` \| `claude` \| `openrouter` |
| `GEMINI_API_KEY`  | *(empty)*            | required for the default provider  |
| `GEMINI_MODEL`    | `gemini-2.5-flash`   |                                    |
| `OPENAI_API_KEY`  | *(empty)*            | only if `AI_PROVIDER=openai`       |
| `ANTHROPIC_API_KEY` | *(empty)*          | only if `AI_PROVIDER=claude`       |
| `AI_HTTP_TIMEOUT` | `45`                 | seconds per request                |
| `AI_MAX_RETRIES`  | `2`                  | retries on 429 / 5xx / timeout     |
| `AI_RATE_PER_MIN` | `15`                 | live generations / user / minute (0 = off) |
| `AI_RATE_PER_DAY` | `200`                | live generations / user / day (0 = off)    |

Get a Gemini key at https://aistudio.google.com/apikey.

## 2. Switching providers

Set `AI_PROVIDER=openai` (or `claude` / `openrouter`) and provide that
provider's key. Nothing else changes.

## 3. Adding a new AI feature

1. `includes/ai/prompts/<feature>.php` — define `ai_prompt_<feature>($input)`
   and `ai_shape_<feature>($decoded)`.
2. `api/ai/<feature>.php` — 4 lines calling `ai_run_feature()`.
3. (App) add one entry to `src/config/aiTools.js`.

## 4. Adding a new provider

Implement `AiProviderInterface` (extend `BaseHttpProvider` for retry/timeout for
free) and add a `case` to `AiProviderFactory::make()`. Done.

## Architecture

```
api/ai/<feature>.php ──> ai_run_feature() ──> AiService
                                                 │
        PromptBuilder ◄──────┬───────────────────┤
     (prompts/<feature>.php) │                    ├──> AiProviderFactory ──> AiProviderInterface
                             │                    │            (Gemini | OpenAI | Claude | OpenRouter)
                       AiCache / AiHistory ◄──────┘            all extend BaseHttpProvider
```

- **Cache:** unchanged input returns the stored result; `regenerate:true`
  bypasses + overwrites.
- **History:** every generation (success or error) is logged; `save.php`
  bookmarks a row.
- **Errors:** `AiException` carries a user-safe message + HTTP status; keys are
  never logged or returned.
