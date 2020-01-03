<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
    
<xsl:output method="html"/>

<xsl:template match="branches">
    <ul>
		<li>
		<xsl:apply-templates select="branch" />
		<li>
    </ul>
</xsl:template>

<xsl:template match="specialties">
   <ul>
        <li>
            <xsl:apply-templates select="specialty" />
        </li>       
   </ul>
</xsl:template>

</xsl:stylesheet>
