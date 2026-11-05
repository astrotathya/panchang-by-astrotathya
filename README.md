# Panchang by AstroTathya (WordPress plugin)

Show today's Hindu panchang and upcoming festivals for a chosen city on a WordPress site: tithi and nakshatra with their end times, sunrise and sunset, Rahu Kaal, Abhijit, and the coming vrats and festivals, in Hindi or English. It works as a block, a classic widget or a shortcode.

The plugin is the embed code only. The panchang is calculated by [AstroTathya](https://astrotathya.com) and shown in a sandboxed iframe from astrotathya.com. Each site needs a free site key, self-serve at [astrotathya.com/panchang-widget](https://astrotathya.com/panchang-widget); without one the plugin shows visitors nothing.

- Directory listing, usage, shortcode attributes and the full list of external requests: [`readme.txt`](readme.txt)
- Requires WordPress 6.3+ and PHP 7.4+
- Licence: GPL-2.0-or-later ([`LICENSE`](LICENSE)). The licence covers this plugin's code. The panchang data and the widget pages served from astrotathya.com are not part of this repository and are not covered by it.
- Trademarks: "AstroTathya", the AstroTathya logo and the directory icon and banners in `.wordpress-org/` identify AstroTathya and are not licensed under the GPL. Forks are welcome under the GPL, but must use a different name and logo and must not suggest they are made or endorsed by AstroTathya.

## Building the release zip

```sh
sh bin/build-zip.sh   # writes dist/panchang-by-astrotathya.zip
```

The zip contains the tracked files except `bin/`, `.wordpress-org/` (directory listing images), `README.md` and `SECURITY.md`.

## Issues and contributions

Bug reports and small fixes are welcome as GitHub issues and pull requests. For a site key, a new city or anything about the panchang itself, write to hello@astrotathya.com.

Security problems: see [`SECURITY.md`](SECURITY.md).
