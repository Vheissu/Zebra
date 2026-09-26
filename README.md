# Zebra

A social news site you run yourself. People submit links or ask questions, everyone votes, the good stuff floats to the top and the comments sort themselves into threads. If you've used Hacker News or an early Reddit you already know how it works.

Zebra started in 2012 as a CodeIgniter app that never quite got finished. This is the finished version, rebuilt on Laravel 13. The old code is still in the git history on `master` if you want to see where it came from.

## What it does

- Link posts and text posts ("Ask"), with duplicate detection: submit a link that's already been posted in the last 30 days and you land on the existing discussion.
- Front page ranked by score and age, plus New, Ask, recent comments, and a page for every domain.
- Threaded comments with replies, collapsing, permalinks, and deleted comments that keep their replies visible.
- Up and down votes on stories and comments. Downvotes need a reason and, for regular members, a minimum karma (20 by default). You can't vote on your own posts, and karma only counts other people's votes.
- Accounts with usernames, profiles, settings, and password reset by email.
- A moderation area: stats, a feed of recent downvotes and their reasons, delete and restore for stories and comments, banning, and admin rights.
- A JSON API with personal access tokens.

Everything works without JavaScript. The small script in `resources/js/app.js` makes voting and replying happen in place rather than reloading the page.

## Getting started

You need PHP 8.3+, Composer and Node.

```sh
composer run setup
composer run dev
```

`setup` installs dependencies, builds the front end, and runs the installer. The installer asks which database to use (SQLite needs nothing else; MySQL and MariaDB work too), migrates, creates your admin account, and offers to load sample content. You can skip the questions:

```sh
php artisan zebra:install --db=sqlite --admin=you --email=you@example.com --password=secret123 --demo
```

The sample content is the 24 stories that were in Zebra's database in September 2012, moved forward to this week, with six demo accounts (`zebra`, `maxxx`, `galazy`, `okapi`, `quagga`, `tapir`). They all use the password `password`, so don't load them on a public site.

Password reset emails go to the log (`storage/logs/laravel.log`) until you set the `MAIL_*` values in `.env`.

## Configuration

The Zebra-specific settings live in `config/zebra.php` and can be set from `.env`:

| Setting | Default | What it does |
| --- | --- | --- |
| `ZEBRA_TAGLINE` | Links worth reading... | Shown in the page title and footer |
| `ZEBRA_PER_PAGE` | 30 | Stories or comments per page |
| `ZEBRA_RANK_GRAVITY` | 45000 | Seconds of age that cost a story a factor of ten in score. Lower turns the front page over faster |
| `ZEBRA_DOWNVOTE_KARMA` | 20 | Karma needed to downvote. Admins are exempt |
| `ZEBRA_EDIT_WINDOW` | 120 | Minutes an author can edit a post for. Admins are exempt |
| `ZEBRA_DUPLICATE_DAYS` | 30 | How long a link counts as a duplicate |

Each story's rank is stored on the row, so if you change the gravity, run `php artisan zebra:rerank` to recalculate the existing ones.

## The API

Reading needs nothing. Writing needs a token, which you can create on your settings page or by posting your username and password to `/api/v1/tokens`. Send it as `Authorization: Bearer <token>`.

| Method | Path | |
| --- | --- | --- |
| GET | `/api/v1/stories?sort=hot\|new\|ask&per_page=30` | List stories |
| GET | `/api/v1/stories/{id}` | A story with its full comment tree |
| POST | `/api/v1/stories` | Submit `title`, and `url` and/or `text`. A recent duplicate gets a 409 with the existing story |
| PATCH, DELETE | `/api/v1/stories/{id}` | Edit or delete your story |
| GET | `/api/v1/comments` | Newest comments |
| GET | `/api/v1/comments/{id}` | A comment and its replies |
| POST | `/api/v1/stories/{id}/comments` | Comment with `body`, and `parent_id` to reply |
| PATCH, DELETE | `/api/v1/comments/{id}` | Edit or delete your comment |
| POST | `/api/v1/stories/{id}/vote`, `/api/v1/comments/{id}/vote` | `direction` of `up`, `down` or `none`, plus `reason` for downvotes |
| GET | `/api/v1/vote-reasons` | The reasons a downvote can give |
| GET | `/api/v1/users/{username}`, `/api/v1/me` | Profiles |
| POST, DELETE | `/api/v1/tokens`, `/api/v1/tokens/current` | Get a token, revoke the one you're using |

The API follows the same rules as the site: the same edit window, karma threshold and rate limits, and banned accounts get a 403.

## Where things are

- `app/Actions`: submitting, commenting and voting, shared by the web controllers and the API
- `app/Models/Story.php`: the ranking formula
- `app/Support/Formatter.php`: how comment text becomes HTML (paragraphs, `*italics*`, indented code, auto-linked URLs, everything else escaped)
- `resources/views` and `resources/css/app.css`: the theme. It's plain Blade and plain CSS, so restyling it doesn't need a build tool you haven't already got.

## Tests

```sh
composer test
```

The suite runs on SQLite in memory. It passes on MySQL too:

```sh
DB_CONNECTION=mysql DB_DATABASE=zebra_test php artisan test
```
