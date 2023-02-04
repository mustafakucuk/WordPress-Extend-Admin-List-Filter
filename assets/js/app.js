(function ($) {
  $(document).ready(function () {
    const uiSelects = $(".wealf-select-ui");

    const ajaxRequest = function (action, data, success) {
      $.ajax({
        url: wealf.ajax_url,
        data: {
          action: "wealf_ajax",
          security: wealf.ajax_nonce,
          action_name: action,
          data,
        },
        type: "POST",
        success,
      });
    };

    uiSelects.each(function () {
      const select = $(this);
      const isAjax = select.data("is-ajax");
      const callbackName = select.data("callback");
      const selectedValue = select.data("selected");
      let uiOptions = select.data("ui-options");
      uiOptions =
        typeof uiOptions === "string" ? JSON.parse(uiOptions) : uiOptions;

      const valueField = uiOptions.valueField;

      new TomSelect(select[0], {
        ...uiOptions,
        load: function (query, callback) {
          if (!isAjax) {
            callback();
            return;
          }

          if (self.loading > 1) return callback();

          if (!callbackName) {
            callback();
            return;
          }

          ajaxRequest(
            "get_select_options",
            { query, callback: callbackName },
            function (response) {
              callback(response);
            }
          );
        },
        onInitialize: function () {
          const self = this;

          if (!callbackName) {
            callback();
            return;
          }

          if (selectedValue) {
            ajaxRequest(
              "get_select_options",
              { query: "", callback: callbackName },
              function (response) {
                if (!response.success) {
                  return;
                }

                const selectedOption = response.data.find(
                  (option) => option[valueField] == selectedValue
                );

                if (selectedOption) {
                  self.addOption(selectedOption);
                  self.setValue(selectedValue);
                  self.refreshOptions(false);
                }
              }
            );
          }
        },
      });
    });
  });
})(jQuery);
