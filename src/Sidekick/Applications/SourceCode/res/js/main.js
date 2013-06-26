window.exports = {
  highlightLine: function(){
    line = $($(".linenums li")[lineNumber]);
    line.css("background-color","brown");
    $('html, body').animate({
      scrollTop: line.offset().top-43
    }, 2000);
  }
};
