jQuery(function ($) {
  // Initialize CodeMirror on all our textareas
  if (!window.wp || !wp.codeEditor || !wp.codeEditor.initialize) return;

  $('.rwmb-code-field').each(function () {
    var el = this;
    var mode = $(el).attr('data-code-mode') || 'css';
    var height = $(el).attr('data-code-height') || '300px';

    var settings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
    settings.codemirror = settings.codemirror || {};
    settings.codemirror.mode = mode;
    settings.codemirror.lineNumbers = true;
    settings.codemirror.indentUnit = 2;
    settings.codemirror.tabSize = 2;

    var editor = wp.codeEditor.initialize(el, settings);
    if (editor && editor.codemirror) {
      editor.codemirror.setSize(null, height);
    }
  });
});
