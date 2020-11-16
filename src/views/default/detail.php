<?php
/**
 * @var \yii\web\View $this
 * @var \kriss\logReader\Log $log
 */

?>
<div class="form-horizontal">
    <div></div>
</div>
<pre id="log-content-container" style="overflow: scroll;display: none;width: 100%;height: 100%">
    <?= file_get_contents($log->getFileName()) ?>
</pre>
<?php
$js = <<<JS
var logContentEl = $('#log-content-container');
window.logContentEl = logContentEl;
window._logContentEl = {
  _originContent: logContentEl.text(),
  resize() {
    logContentEl.hide()
    logContentEl.width($(window).width() - logContentEl.parent().offset().left - 50)
    logContentEl.height($(window).height() - logContentEl.parent().offset().top - 50)
    logContentEl.show()
  },
  // web\User 使用 web\\\\User
  // abc.*xyz
  // abc|xyz
  _highlightPoint: 0,
  highlight(keyword) {
    logContentEl.html(this._originContent.replace(new RegExp('('+keyword+')', 'g'), '<span class="highlight" style="background-color:orange;">$1</span>'))
    this._highlightPoint = -1
    this.highlightNext()
  },
  highlightNext(point = null) {
    if ($('.highlight').length === 0) {
      return 
    }
    this._highlightPoint = point ? point : this._highlightPoint + 1
    if (this._highlightPoint >= $('.highlight').length) {
      this._highlightPoint = 0
    }
    console.log(this._highlightPoint)
    logContentEl.scrollTop(0)
    logContentEl.scrollTop($('.highlight').eq(this._highlightPoint).offset().top)
  },
  highlightPrev(point = null) {
    if ($('.highlight').length === 0) {
      return 
    }
    this._highlightPoint = point ? point : this._highlightPoint - 1
    if (this._highlightPoint < 0) {
      this._highlightPoint = $('.highlight').length - 1
    }
    console.log(this._highlightPoint)
    logContentEl.scrollTop(0)
    logContentEl.scrollTop($('.highlight').eq(this._highlightPoint).offset().top)
  }
}

$(window).resize(function () {
  _logContentEl.resize();
})
_logContentEl.resize();
JS;
$this->registerJs($js);

