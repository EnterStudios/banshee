<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-striped table-hover table-condensed overview">
<thead>
<tr>
<th>Title</th>
<xsl:if test="/output/user/@admin='yes'">
<th>Author</th>
</xsl:if>
<th>Visible</th>
<th>Timestamp</th>
<th>Comments</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="weblogs/weblog">
<tr onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="title" /></td>
<xsl:if test="/output/user/@admin='yes'">
<td><xsl:value-of select="author" /></td>
</xsl:if>
<td><xsl:value-of select="visible" /></td>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="comments" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="right">
<xsl:apply-templates select="pagination" />
</div>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New weblog</a>
<a href="/cms" class="btn btn-default">Back</a>
<xsl:if test="comments='yes'">
<a href="/cms/weblog/comment" class="btn btn-default">Comments</a>
</xsl:if>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="weblog/@id">
<input type="hidden" name="id" value="{weblog/@id}" />
</xsl:if>
<label for="title">Title:</label>
<input type="text" id="title" name="title" value="{weblog/title}" class="form-control" />
<label for="editor">Post:</label>
<textarea id="editor" name="content" class="form-control"><xsl:value-of select="weblog/content" /></textarea>

<!-- Tags -->
<div class="tags">
<label>Tags:</label>
<xsl:for-each select="tags/tag">
<span>
<input type="checkbox" name="tag[]" value="{@id}">
<xsl:if test="@selected='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
</input>
<xsl:value-of select="." />
</span>
</xsl:for-each></div>

<label for="newtag">New tags:</label>
<input type="text" id="newtag" name="new_tags" value="{weblog/new_tags}" class="form-control" />

<label for="visible">Visible:</label>
<input type="checkbox" id="visible" name="visible">
<xsl:if test="weblog/visible='yes'">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
</input>

<!-- Buttons -->
<div class="btn-group">
<input type="submit" name="submit_button" value="Save weblog" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="weblog/@id">
<input type="submit" name="submit_button" value="Delete weblog" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>

<!-- Comments -->
<h2>Comments</h2>
<p>Selected comments will be deleted.</p>
<table class="table table-striped table-hover table-condensed comments">
<thead>
<tr><th></th><th>Author</th><th>Content</th></tr>
</thead>
<tbody>
<xsl:for-each select="comments/comment">
<tr><td><input type="checkbox" name="comment[]" value="{@id}" /></td><td><xsl:value-of select="author" /></td><td><xsl:value-of select="content" /></td></tr>
</xsl:for-each>
</tbody>
</table>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1><img src="/images/icons/weblog.png" class="title_icon" />Weblog administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
