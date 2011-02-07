<script type="text/javascript">
	function insertPagebreak(editor)
	{
		// Get the pagebreak title
		var title = document.getElementById("title").value;
		if (title != '') {
			title = "title=\""+title+"\" ";
		}
		// Get the pagebreak toc alias -- not inserting for now
		// don't know which attribute to use...
		var alt = document.getElementById("alt").value;
		if (alt != '') {
			alt = "alt=\""+alt+"\" ";
		}
		var tag = "<hr class=\"system-pagebreak\" "+title+" "+alt+"/>";
		window.parent.jInsertEditorText(tag, '<?php echo preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', JRequest::getVar('e_name') ); ?>');
		window.parent.document.getElementById('sbox-window').close();
		return false;
	}
</script>
<form>
<div align="center">
<div class="key" align="right"><label for="title"><?php echo JText::_( 'PGB PAGE TITLE' ); ?></label></div>
<div><input type="text" id="title" name="title" /></div>
<div class="key" align="right"><label for="alias"><?php echo JText::_( 'PGB TOC ALIAS PROMPT' ); ?></label></div>
<div><input type="text" id="alt" name="alt" /></div>
</div>
</form>
<button onclick="insertPagebreak();"><?php echo JText::_( 'PGB INS PAGEBRK' ); ?></button>