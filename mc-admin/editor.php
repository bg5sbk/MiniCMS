<?php function editor($content) { ?>
    <div id="editor" class="edit_textarea"><?php echo htmlspecialchars($content); ?></div>
    <input type="hidden" id="input_content" name="content"/>
    <script src="ace-builds/src-min/ace.js" type="text/javascript" charset="utf-8"></script>
    <script>
      var editor = ace.edit("editor");
      editor.getSession().setMode("ace/mode/markdown");
      editor.getSession().setUseWrapMode(true);
      editor.setShowPrintMargin(false);
      editor.renderer.setShowGutter(false);

      var input_content = document.getElementById('input_content');
      editor.getSession().on('change', function(){
        input_content.value = editor.getSession().getValue();
      });

      function getPageSize() {
          var xScroll, yScroll;
          if (window.innerHeight && window.scrollMaxY) {
              xScroll = window.innerWidth + window.scrollMaxX;
              yScroll = window.innerHeight + window.scrollMaxY;
          } else {
              if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac    
                  xScroll = document.body.scrollWidth;
                  yScroll = document.body.scrollHeight;
              } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari    
                  xScroll = document.body.offsetWidth;
                  yScroll = document.body.offsetHeight;
              }
          }
          var windowWidth, windowHeight;
          if (self.innerHeight) { // all except Explorer    
              if (document.documentElement.clientWidth) {
                  windowWidth = document.documentElement.clientWidth;
              } else {
                  windowWidth = self.innerWidth;
              }
              windowHeight = self.innerHeight;
          } else {
              if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode    
                  windowWidth = document.documentElement.clientWidth;
                  windowHeight = document.documentElement.clientHeight;
              } else {
                  if (document.body) { // other Explorers    
                      windowWidth = document.body.clientWidth;
                      windowHeight = document.body.clientHeight;
                  }
              }
          }       
          // for small pages with total height less then height of the viewport    
          if (yScroll < windowHeight) {
              pageHeight = windowHeight;
          } else {
              pageHeight = yScroll;
          }    
          // for small pages with total width less then width of the viewport    
          if (xScroll < windowWidth) {
              pageWidth = xScroll;
          } else {
              pageWidth = windowWidth;
          }
          arrayPageSize = new Array(pageWidth, pageHeight, windowWidth, windowHeight);
          return arrayPageSize;
      }

      function resizeEditor() {
        var e = document.getElementById('editor');
        e.style.height = (getPageSize()[3] - e.offsetTop - 250) + 'px';
        editor.resize();
      }

      window.onload = resizeEditor;
      window.onresize = resizeEditor;
    </script>
<?php } ?>
