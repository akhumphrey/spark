<?xml version="1.0" encoding="iso-8859-1"?>
<Q:stylesheet version="1.0" xmlns:Q="http://www.w3.org/1999/XSL/Transform" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:rss="http://purl.org/rss/1.0/" xmlns="http://www.w3.org/1999/xhtml">
	<Q:output method="html" />
	<Q:template match="/">
		<Q:element name="html">
			<Q:attribute name="class">RssToHtmlByXsl</Q:attribute>
			<head>
				<Q:element name="meta">
					<Q:attribute name="content-type">text/html; charset=utf-8</Q:attribute>
				</Q:element>

				<Q:element name="link">
					<Q:attribute name="rel">stylesheet</Q:attribute>
					<Q:attribute name="href">../spark/styles/rss.css</Q:attribute>
					<Q:attribute name="type">text/css</Q:attribute>
				</Q:element>

				<Q:for-each select="/rss/channel/title">
					<title><Q:value-of select="."/></title>
				</Q:for-each>

			</head>

			<body onload="go_decoding();">

				<div id="cometestme" style="display: none;">
					<Q:text disable-output-escaping="yes">&amp;</Q:text>
				</div>

				<Q:element name="script">
					<Q:attribute name="type">text/javascript</Q:attribute>
					<Q:attribute name="src">../spark/scripts/xsl.js</Q:attribute>
				</Q:element>

				<h1 class="feedtitle">
					<a href="{/rss/channel/link}"><Q:value-of select="/rss/channel/title"/></a>
				</h1>

				<Q:for-each select="/rss/channel/description">
					<Q:if test=". != /rss/channel/title" >
						<p class='desc'><Q:value-of select="."/></p>
					</Q:if>
				</Q:for-each>

				<Q:variable name="C" select="count(/rss/channel/item)" />

				<p class='leadIn'>
					Currently
					<Q:choose>
						<Q:when test="$C = 0" >no items </Q:when>
						<Q:when test="$C = 1" >one item </Q:when>
						<Q:otherwise>
							<Q:value-of select="$C" /> items
						</Q:otherwise>
					</Q:choose>
					in this feed:
				</p>

				<dl class='Items'>

					<Q:if test='$C = 0'>
						<dt>(Empty)</dt>
					</Q:if>

					<Q:for-each select="/rss/channel/item">

						<dt>
							<a href="{link}">
								<Q:choose>
									<Q:when test="title"><Q:value-of select="title"/></Q:when>
									<Q:otherwise><em>(No title)</em></Q:otherwise>
								</Q:choose>
							</a>
						</dt>

						<Q:if test="description" >
							<dd name="decodeme"><Q:value-of disable-output-escaping="yes" select="description" /></dd>
						</Q:if>

					</Q:for-each>
				</dl>
			</body>
		</Q:element>
	</Q:template>
</Q:stylesheet>