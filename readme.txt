=== Panchang by AstroTathya ===
Contributors: astrotathya
Tags: panchang, hindu calendar, hindu festivals, rahu kaal, ekadashi
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Daily Hindu panchang for any city: tithi, nakshatra, Rahu Kaal, festivals and vrats, in Hindi or English. Needs a free site key.

== Description ==

Put today's panchang on your temple or community website, and stop typing festival dates into it by hand.

The panchang card shows the day's tithi and nakshatra with the times they end, sunrise and sunset, Rahu Kaal, Abhijit and the next festival. The festivals widget lists the vrats and festivals coming up. Both are worked out for the city you choose, in that city's own time.

That matters more than it sounds. In 2026, Bhai Dooj falls on Tuesday 10 November in New York and on Wednesday 11 November in New Delhi. A temple in New Jersey should show the first date, and with this plugin it does. The same goes for Ekadashi, Purnima, Amavasya, Navratri and Diwali.

You can show it in Hindi or English, or let it follow your site's language. Add it as a block, a classic widget or a shortcode, in a light, dark or maroon theme, as a card or a slim banner. Nothing to update: the dates and times move on by themselves every day.

Each site needs a free site key, entered once under Settings > Panchang. Get it at https://astrotathya.com/panchang-widget: sign in, add the verification line it gives you to your site (in a post, a page or the theme's head), and the key is issued in a minute. A key covers your site's address and up to three cities you choose. Until a key is entered, the plugin shows visitors nothing where a widget is placed, and administrators see an "Add your site key" note instead.

If your community says panchangam, or Rahu Kalam, it is the same thing.

The panchang is calculated by AstroTathya and shown from astrotathya.com. "External services" below explains exactly what that means for your visitors.

= हिंदी में =

अपने मंदिर या संस्था की वेबसाइट पर आज का पंचांग लगाइए, और त्योहारों की तारीख़ें हाथ से लिखने का झंझट ख़त्म कीजिए। तिथि, नक्षत्र, सूर्योदय-सूर्यास्त, राहु काल और आने वाले पर्व आपके चुने हुए शहर के हिसाब से, उसी के समय में दिखते हैं, हिंदी या अंग्रेज़ी में। ब्लॉक, विजेट या शॉर्टकोड, जैसे चाहें जोड़िए। हर साइट के लिए एक मुफ़्त साइट कुंजी (site key) चाहिए, जो astrotathya.com/panchang-widget पर साइन इन करके और अपनी साइट पर एक पुष्टि वाली पंक्ति जोड़कर मिलती है; इसे Settings > Panchang में एक बार डालना होता है।

= Usage =

* **Block editor**: add the "Panchang by AstroTathya" block and pick the city, widget, language, theme and size in the block sidebar.
* **Widgets**: add the "Panchang by AstroTathya" widget to a sidebar or footer.
* **Shortcode**: `[astrotathya_panchang city="mumbai" type="panchang" lang="hi" theme="maroon" size="card"]`

All three need the site key from Settings > Panchang; the city must be one of the cities your key covers.

Shortcode attributes, all optional (anything left out uses the defaults from Settings > Panchang):

* `city`: city slug as in astrotathya.com/panchang/mumbai, for example `mumbai`, `new-delhi`, `varanasi`. It must be one of the cities your site key covers.
* `type`: `panchang` or `festivals`.
* `lang`: `en`, `hi` or `auto` (the site's language).
* `theme`: `light`, `dark` or `maroon`.
* `size`: `card` or `banner`.

= External services =

This plugin displays content from AstroTathya (https://astrotathya.com), a panchang and Hindu calendar service. The plugin itself does not calculate the panchang; without this service it shows nothing.

What is loaded and when:

* **On public pages that contain the block, widget or shortcode**, the visitor's browser loads an iframe from `https://astrotathya.com/embed/panchang/{city}` or `https://astrotathya.com/embed/festivals/{city}`, with the language, theme and size when they are not the defaults, and your site key as `k`. Nothing is loaded while no site key is set. The iframe is sandboxed and kept separate from your site. If you choose the "Script" embed method in Settings > Panchang, the browser instead loads a script from `https://astrotathya.com/embed.js` on your page, which adjusts the iframe's height to its content; that script runs on your pages, so it is off unless you choose it. Pages without a widget load nothing from AstroTathya.
* **In the WordPress admin**, your server makes no requests: the city list ships with the plugin. On Settings > Panchang, a preview of the widget loads in your own browser from `https://astrotathya.com/embed/`, the same as on a public page.

What is sent: the visitor's browser makes ordinary web requests to astrotathya.com, which include the visitor's IP address, user agent and the referring page address (your page URL), plus the chosen city, widget type, language, theme and size, and your site key. AstroTathya checks the key, and that the page showing the widget is on one of the site addresses the key was issued for, before it shows anything. The plugin sends no other data and adds no cookies or analytics of its own.

View count: each time the widget loads, the widget page itself sends one request to `https://astrotathya.com/api/widget-view` with the host name of the page it is shown on (for example `example.org`, never the full address), and the city, widget type, size, theme and language. AstroTathya uses this only to count how many sites show the widget. The IP address of that request is used only for rate limiting and is not stored; no cookie is set.

AstroTathya terms of use: https://astrotathya.com/terms
AstroTathya privacy policy: https://astrotathya.com/privacy

= Credit link =

The widget shows its own small attribution inside the iframe. The plugin adds nothing else to your public pages. An optional text link ("panchang by AstroTathya", nofollow) under each widget is off by default and can be turned on in Settings > Panchang.

== Installation ==

1. In your WordPress admin go to Plugins > Add New, search for "Panchang by AstroTathya", then click Install and Activate. Or upload the `panchang-by-astrotathya` folder to `/wp-content/plugins/` and activate it.
2. Get a free site key at https://astrotathya.com/panchang-widget (sign in, add the verification line to your site, copy the key).
3. Go to Settings > Panchang, enter the key, and choose your default city, language and theme. Without a key the widgets show nothing.
4. Add the block, the widget or the shortcode where you want the panchang to appear.

== Frequently Asked Questions ==

= My city is not in the list. =

Type its slug in the block's "Or type a city slug" field, or use it in the shortcode, for example `city="haridwar"`. Find the slug by searching for your city on astrotathya.com; it is the last part of the panchang page address. The city must also be covered by your site key: a key covers up to three cities, chosen when you get it.

= Are the timings for my city? =

Yes. Tithi, nakshatra, Rahu Kaal and festival dates are calculated for the chosen city and shown in its local time zone.

= Which panchang system does it follow? Will it match my local calendar? =

Times are calculated for your city's latitude, longitude and time zone, with the Lahiri ayanamsha and the day counted from sunrise to sunrise; that is our convention, the same as the panchang on astrotathya.com. Festival dates follow the rules for each festival (for example the tithi at sunrise, at midday or at night, as that festival requires). A local calendar made for another city, or one that follows a regional tradition, can differ by a day; the "Full panchang" link explains how each date was decided.

= Can I show it in Hindi? =

Yes. Set the language to Hindi, either for the whole site in Settings > Panchang or per block, widget or shortcode (`lang="hi"`). The plugin's admin screens are also translated into Hindi.

= Does it slow down my site? =

The iframe loads lazily, and only on pages that show a widget. If you choose the Script embed method, the script is small and loads asynchronously in the footer, again only on pages with a widget.

= Does the plugin track my visitors? =

The plugin adds no tracking. Visitors' browsers do contact astrotathya.com to show the widget; see "External services".

= Does it work without JavaScript? =

Yes. The default iframe embed needs no script on your page, and with the Script embed method visitors without JavaScript still see the iframe version of the same widget.

= Who can use it? Do I need a key? =

Any temple, community organisation, astrologer or other site that shows the panchang to its readers. Each site needs a free site key from https://astrotathya.com/panchang-widget, issued once you show the site is yours by adding a verification line to it. A key covers your site's address and up to three cities; up to 500 widget views a day, renewed yearly. For a temple TV screen, a site on Google Sites or larger needs, write to hello@astrotathya.com. The plugin is the embed code only: without a key it shows visitors nothing, and wp-admin shows an "Add your site key" notice.

= The widget shows a short card instead of the panchang. =

The key was not accepted for that page: it is mistyped, it does not cover that city or that widget, or the page is on a site address the key was not issued for. Check the key under Settings > Panchang, or write to hello@astrotathya.com.

== Screenshots ==

1. Today's panchang card for Mumbai in Hindi, maroon theme.
2. Upcoming festivals for New York beside a page's text, light theme.
3. Block settings in the editor.
4. Settings > Panchang, with the iframe embed as the default.

== Changelog ==

= 1.0.0 =
* First release: block, classic widget and shortcode for today's panchang and upcoming festivals; Hindi and English; light, dark and maroon themes; card and banner sizes. Each site needs a free site key (Settings > Panchang), from astrotathya.com/panchang-widget, sent with each widget; without one nothing is shown to visitors and administrators see an "Add your site key" notice.

== Upgrade Notice ==

= 1.0.0 =
First release.
