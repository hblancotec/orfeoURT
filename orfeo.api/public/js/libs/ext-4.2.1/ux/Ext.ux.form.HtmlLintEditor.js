
/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.define('Ext.ux.form.HtmlLintEditor', {
extend : 'Ext.form.HtmlEditor',//, {
alias : 'widget.HtmlLintEditor',
dirtyHtmlTags: [
// http://stackoverflow.com/questions/2875027/clean-microsoft-word-pasted-text-using-javascript
// http://stackoverflow.com/questions/1068280/javascript-regex-multiline-flag-doesnt-work
{regex: /<!--[\s\S]*?-->/gi, replaceVal: ""},
// http://www.1stclassmedia.co.uk/developers/clean-ms-word-formatting.php
{regex: /<\\?\?xml[^>]*>/gi, replaceVal: ""},
{regex: /<\/?\w+:[^>]*>/gi, replaceVal: ""}, // e.g. <o:p... 
{regex: /\s*MSO[-:][^;"']*/gi, replaceVal: ""},
{regex: /\s*MARGIN[-:][^;"']*/gi, replaceVal: ""},
{regex: /\s*PAGE[-:][^;"']*/gi, replaceVal: ""},
{regex: /\s*TAB[-:][^;"']*/gi, replaceVal: ""},
{regex: /\s*LINE[-:][^;"']*/gi, replaceVal: ""},
{regex: /\s*FONT-SIZE[^;"']*/gi, replaceVal: ""},
{regex: /\s*LANG=(["'])[^"']*?\1/gi, replaceVal: ""},
        {regex: /<(P|H\d)[^>]*>([\s\S]*?)<\/\1>/gi, replaceVal: "<br>$2"},

{regex: /\s*\w+=(["'])((&nbsp;|\s|;)*|\s*;+[^"']*?|[^"']*?;{2,})\1/gi, replaceVal: ""},
{regex: /<span[^>]*>(&nbsp;|\s)*<\/span>/gi, replaceVal: ""},
        {regex: /<(\/?td style|\/?td width|\[)[^>]*?>/gi, replaceVal: "<td>"},
        {regex: /<(\/?span style|\[)[^>]*?>/gi, replaceVal: "<span>"},
//{regex: /<([^\s>]+)[^>]*>(&nbsp;|\s)*<\/\1>/gi, replaceVal: ""},
// http://www.codinghorror.com/blog/2006/01/cleaning-words-nasty-html.html
{regex: /<(\/?title|\/?meta|\/?style|\/?st\d|\/?head|\/?html|\/?body|!\[)[^>]*?>/gi, replaceVal: ""},
{regex: /(\n(\r)?){2,}/gi, replaceVal: ""}
],
 
syncValue : function(){
    if(this.initialized){
    var bd = this.getEditorBody();
    var html = bd.innerHTML;

    if (this.hasDirtyHtmlTags(html)){
    // Note: the selection will be lost...
                bd.innerHTML = this.cleanHtml(html);
    if(Ext.isGecko){
    // Gecko hack, see: https://bugzilla.mozilla.org/show_bug.cgi?id=232791#c8
    this.setDesignMode(false); //toggle off first
    this.setDesignMode(true);
    }
    }
    }

    Ext.ux.form.HtmlLintEditor.superclass.syncValue.call(this);
},
 
hasDirtyHtmlTags: function(html){
    if (!html) return;

    var hasDirtyHtmlTags = false;
    Ext.each(this.dirtyHtmlTags, function(tag, idx){
    return !(hasDirtyHtmlTags = html.match(tag.regex));
    });
    return hasDirtyHtmlTags;
},
 
cleanHtml: function(html) {
    if (!html) return;

    Ext.each(this.dirtyHtmlTags, function(tag, idx){
    html = html.replace(tag.regex, tag.replaceVal);
    });

    // http://www.tim-jarrett.com/labs_javascript_scrub_word.php
        html = html.replace(new RegExp(String.fromCharCode(8220), 'gi'), '"'); //?
        html = html.replace(new RegExp(String.fromCharCode(8221), 'gi'), '"'); //?
        html = html.replace(new RegExp(String.fromCharCode(8216), 'gi'), "'"); //?
        html = html.replace(new RegExp(String.fromCharCode(8217), 'gi'), "'"); //?
    html = html.replace(new RegExp(String.fromCharCode(8211), 'gi'), "-"); //?
    html = html.replace(new RegExp(String.fromCharCode(8212), 'gi'), "--"); //?
    html = html.replace(new RegExp(String.fromCharCode(189), 'gi'), "1/2"); //�
    html = html.replace(new RegExp(String.fromCharCode(188), 'gi'), "1/4"); //�
    html = html.replace(new RegExp(String.fromCharCode(190), 'gi'), "3/4"); //�
    html = html.replace(new RegExp(String.fromCharCode(169), 'gi'), "(C)"); //�
    html = html.replace(new RegExp(String.fromCharCode(174), 'gi'), "(R)"); //�
    html = html.replace(new RegExp(String.fromCharCode(8230), 'gi'), "..."); //?
    
    return Ext.ux.form.HtmlLintEditor.superclass.cleanHtml.call(this, html);
    }
});
