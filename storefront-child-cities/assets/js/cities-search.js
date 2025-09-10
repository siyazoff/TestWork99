(function ($) {
  "use strict";

  function debounce(fn, wait) {
    let t;
    return function () {
      const ctx = this,
        args = arguments;
      clearTimeout(t);
      t = setTimeout(function () {
        fn.apply(ctx, args);
      }, wait);
    };
  }

  const $input = $("#cities-search-input");
  const $tbody = $("#cities-table-body");

  function search() {
    const q = $input.val() || "";
    $.ajax({
      type: "POST",
      url: CitiesSearch.ajaxUrl,
      dataType: "json",
      data: {
        action: "cities_search",
        nonce: CitiesSearch.nonce,
        q: q,
      },
      beforeSend: function () {
        $tbody.html('<tr><td colspan="3">...</td></tr>');
      },
      success: function (resp) {
        if (resp && resp.success && resp.data && resp.data.html !== undefined) {
          $tbody.html(resp.data.html);
        } else {
          $tbody.html('<tr><td colspan="3"><em>Error</em></td></tr>');
        }
      },
      error: function () {
        $tbody.html('<tr><td colspan="3"><em>Request failed</em></td></tr>');
      },
    });
  }

  $input.on("input", debounce(search, 300));
})(jQuery);
