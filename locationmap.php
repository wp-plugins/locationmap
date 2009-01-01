<?php
/*
Plugin Name: Locationmap
Plugin URI: http://www.oceanius.com/
Description: Locationmap plots an image from specified xml data.
Version: 1.0
Author: Martijn de Boer
Author URI: http://sexybiggetje.nl/
*/

/*
    Copyright 2009  Martijn de Boer  (email : martijn@oceanius.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function locationmap_admin_menu()
{
  add_options_page('Locationmap Options', 'Locationmap', 8, __FILE__, 'locationmap_config_options');
}
add_action('admin_menu', 'locationmap_admin_menu');

function locationmap_config_options()
{
	?>
	<div class="wrap">
	<h2>Locationmap</h2>

	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>

	<table class="form-table">

	<tr valign="top">
	<th scope="row"><?php _e('Example') ?></th>
	<td><pre>
&lt;locationmap&gt;
    &lt;map id=&quot;networkmap&quot;&gt;
        &lt;baseimage src=&quot;%pluginpath%/worldmap.png&quot; /&gt;
        &lt;plotimage src=&quot;%pluginpath%/location.png&quot; /&gt;
        &lt;font src=&quot;%pluginpath%/SEMPRG__.TTF&quot; /&gt;
        &lt;location id=&quot;hana&quot; title=&quot;hana.oceanius.com&quot; x=&quot;148&quot; y=&quot;61&quot; /&gt;
        &lt;location id=&quot;wraith&quot; title=&quot;wraith.oceanius.com&quot; x=&quot;82&quot; y=&quot;72&quot; /&gt;
        &lt;location id=&quot;kane&quot; title=&quot;kane.oceanius.com&quot; x=&quot;128&quot; y=&quot;49&quot; /&gt;
        &lt;location id=&quot;adaro&quot; title=&quot;adaro.oceanius.com&quot; x=&quot;250&quot; y=&quot;46&quot; /&gt;
        &lt;link from=&quot;adaro&quot; to=&quot;hana&quot; /&gt;
        &lt;link from=&quot;wraith&quot; to=&quot;hana&quot; /&gt;
        &lt;link from=&quot;kane&quot; to=&quot;hana&quot; /&gt;
    &lt;/map&gt;
&lt;/locationmap&gt;</pre>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row"><?php _e('Locationmap XML') ?></th>
	<td><textarea cols="80" rows="15" name="locationmap-xml"><?php echo get_option('locationmap-xml'); ?></textarea></td>
	</tr>

	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="locationmap-xml" />

	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
	</div>
	<?php
}

function getLocationMap($aMapId)
{
	$mapdata = array();
	$locations = array();
	$links = array();
	
	$locationmap = simplexml_load_string(get_option("locationmap-xml"));
	
	foreach($locationmap->map as $map)
	{
		if ($map->attributes()->id == $aMapId)
		{
			foreach($map->children() as $key=>$value)
			{
				switch(strtolower($key))
				{
					case "baseimage":
						$mapdata["baseimage"] = str_replace("%pluginpath%",WP_PLUGIN_DIR . "/locationmap","" . $value->attributes()->src);
						break;

					case "plotimage":
						$mapdata["plotimage"] = str_replace("%pluginpath%",WP_PLUGIN_DIR . "/locationmap","" . $value->attributes()->src);
						break;

					case "font":
						$mapdata["font"] = str_replace("%pluginpath%",WP_PLUGIN_DIR . "/locationmap","" . $value->attributes()->src);
						break;
						
					case "location":
						$locations["" . $value->attributes()->id] = array("x" => $value->attributes()->x,
								 									 "y" => $value->attributes()->y,
																	 "title" => $value->attributes()->title);
						break;

					case "link":
						$links[] = array("from" => $value->attributes()->from,
										 "to" => $value->attributes()->to);
						break;

					default:
						break;
				}
			}
		}
	}

	$size = getimagesize($mapdata["baseimage"]);
	$im = imagecreatefrompng($mapdata["baseimage"]);
	imagealphablending($im, true);
	imagesavealpha($im, true);
	
	$locationsize = getimagesize($mapdata["plotimage"]);
	$locationim = imagecreatefrompng($mapdata["plotimage"]);
	imagealphablending($locationim, true);
	imagesavealpha($locationim, true);
	
	$linecolor = imagecolorallocate($im, 65, 65, 65);
	
	foreach($links as $link)
	{
		imageline(  $im, 
					$locations["" . $link["from"]]["x"], $locations["" . $link["from"]]["y"] + 1,
					$locations["" . $link["to"]]["x"], $locations["" . $link["to"]]["y"] + 1,
					$linecolor);
	}

	foreach($locations as $location)
	{
		$posx	= $location["x"] - ($locationsize[0] / 2);
		$posy	= $location["y"] - ($locationsize[1] / 2);
		$font	= $mapdata["font"];
		$size	= 6;
		$bbox	= imagettfbbox($size, 0, $font, $location["title"]);
		$width	= $bbox[2];
		$height	= abs($bbox[5]);
		$x		= $location["x"] - ($width/2);
		$y		= $location["y"] - $locationsize[1];
		imagecopyresampled($im, $locationim, $posx, $posy, 0, 0, $locationsize[0], $locationsize[1], $locationsize[0], $locationsize[1]);

		imagettftext($im, $size, 0, $x, $y, $color, $font, $location["title"]);

	}
	
	ob_start();
	imagepng($im);
	$theImage = ob_get_contents();
	ob_end_clean();
	imagedestroy($im);

	return array($theImage,$size[0],$size[1]);
}

function locationmap($aMapId, $echo = true)
{
	$imageData = getLocationMap($aMapId);
	$aString = "<p class=\"Locationmap\"><img src=\"data:image/png;base64,".base64_encode($imageData[0])."\" width=\"".$imageData[1]."\" height=\"".$imageData[2]."\" /></p>";
	
	if ($echo)
		echo $aString;
		
	return $aString;
}

function locationmap_filter($content)
{
	$tag = "%locationmap: ";
	$lpos = 0;

	while (strpos($content, $tag) !== false)
	{
		$lpos = strpos($content, $tag);
		$rpos = strpos($content, "%",$lpos+strlen($tag));
		$mapid = substr($content, $lpos+strlen($tag), ($rpos-$lpos)-1);
		$mapid = substr(trim(strip_tags($mapid)), 0, -1);
		if (strpos($mapid, "%") !== false)
			$mapid = substr($mapid, 0, strpos($mapid,"%"));
		$content = str_replace($tag . $mapid . "%", locationmap($mapid, false), $content);
	}
	return $content;
}

add_filter('the_content','locationmap_filter');
?>