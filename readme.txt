=== Locationmap ===
Contributors: sexybiggetje
Tags: location, map, geo

Builds a map from given location, and draws lines between them.

Font is the fine Semplice Regular from Style-Force.
http://pixelfonts.style-force.net/

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

== Installation ==

1) Upload the plugin to your plugins folder, and off you go.
2) Create a valid xml