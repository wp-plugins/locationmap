=== Locationmap ===
Contributors: martijndeboer
Tags: location, map, geo
Stable tag: trunk
Requires at least: 2.5
Tested up to: 2.7
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2199830

Builds a map from given location, and draws lines between them.

== Description ==

Draws a map and inserts points on the location. Has the ability to draw multiple maps. Only accepts PNG as input and output.

Loads a xml from the admin panel, which tells us the following:

- Image to load
- Image to use for plotting a location
- Locations
  - Title
  - x,y

a valid xml should look like the following:
<locationmap>
    <map id="networkmap">
        <baseimage src="%pluginpath%/worldmap.png" />
        <plotimage src="%pluginpath%/location.png" />
        <font src="%pluginpath%/SEMPRG__.TTF" />
        <location id="hana" title="hana.oceanius.com" x="148" y="61" />
        <location id="wraith" title="wraith.oceanius.com" x="82" y="72" />
        <location id="kane" title="kane.oceanius.com" x="128" y="47" />
        <location id="adaro" title="adaro.oceanius.com" x="250" y="46" />
        <link from="adaro" to="hana" />
        <link from="wraith" to="hana" />
        <link from="kane" to="hana" />
    </map>
</locationmap>

Want to show the map? Just put `<?php locationmap("mapid"); ?>` in your template. Don't want to hack a new template or existing template together ?
Just put the following in your post: %locationmap: mapid% .

Font is the fine Semplice Regular from Style-Force.
http://pixelfonts.style-force.net/


== Installation ==

- Upload the plugin to your plugins folder, and off you go.
- Create a valid xml
- Add a call to locationmap("myMapId"); to your template, or a parsable tag to a post / page.

== Frequently Asked Questions ==
- I'm getting no text or a warning about the font.
  - A. Did you specify the font tag ?

- I'd like to have feature x and y, can you implement this ?
  - A. Give me a try, and if I like it, ill add it. You can send your contributions to me, and i'll add them if it fits the plugin.

- I want to define multiple maps, how do I do it.
  - A. Just define multiple <map id="myMapId"> blocks, be sure to have unique id's for each map!