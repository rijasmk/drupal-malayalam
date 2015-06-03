/**
 * @author Roger Padilla C.
 */

(function (window, $, Drupal) {
  'use strict';

  var document = window.document;

  Drupal.behaviors.easyModule = {
    attach: function (context, settings) {

      // Array Remove function.
      if (!Array.prototype.remove) {
        Array.prototype.remove = function(val) {
          var idx = this.indexOf(val);
          this.splice(idx, 1);
        };
      }

      // Cache the select element used for changing the Drupal version.
      var $drupalVersion = $('#edit-drupal-version');
      // Cache the raw dom element for using it in the filtering of hooks.
      var drupalVersion = $drupalVersion[0];

      /* Custom filtering function which will filter hooks by the selected Drupal version. */
      $.fn.dataTableExt.afnFiltering.push(
        function(oSettings, aData, iDataIndex) {
          // Use plain JavaScript as the jQuery val version would be too slow here.
          return aData[2] === drupalVersion.options[drupalVersion.selectedIndex].value;
        }
      );

      // Enhances the hooks table.
      var $hooksTable = $('#easy_module-hooks').dataTable({
        'sScrollY': '250px',
        'bPaginate': false,
        'bScrollCollapse': true,
        'bJQueryUI': true,
        'aoColumnDefs': [
//          {'sSortDataType': 'dom-checkbox', 'aTargets': [0]},
          {'bSortable': false, 'aTargets': [0, 2, 3, 4, 5]},
          {'bSearchable': false, 'aTargets': [0, 2, 4, 5]},
          {'sWidth': '9em', 'aTargets': [4]},
          {'bVisible': false, 'aTargets': [2]}
        ]
      });

      // There is not an provided option to set a title to the datatable.
      $('#easy_module-hooks_filter').before('<span class="hooks-table-title">' + settings.easy_module_tableTitle + '</span>');

      // Empty selected hooks and redraw the hooks table (for filtering hooks).
      $drupalVersion.change(function (evt) {
        hooksSelect.empty();
        $hooksTable.fnDraw();
      });

      // Implements the 'Module' pattern for encapsulating the logic for ticking
      // and unticking hooks to be implemented.
      var hooksSelect = (function () {
        // Private.
        var values = [];
        function updateSelectedHooks() {
          $('#edit-hooks').val(values);
        }
        // Exposed to public.
        return {
          tick: function(val) {
            values.push(val);
            var hookName = $('#easy_module-hook_' + val + '_name').html();
            var choice = '<li class="choice" id="choice_' + val + '">';
            choice += '<span class="choice-label">' + hookName + '</span>';
            choice += '<span class="choice-close">' + val + '</span>';
            choice += '</li>';
            $('#easy_module_hooks_viewer').append(choice);
            updateSelectedHooks();
          },
          untick: function(val) {
            values.remove(val);
            $('#choice_' + val).remove();
            $('#easy_module-hook_' + val).removeAttr('checked');
            updateSelectedHooks();
          },
          empty: function() {
            var i = 0, n = values.length - 1, selHooks = '';
            if(n >= 0) {
              $('#easy_module_hooks_viewer').empty();
              for (; i < n; ++i) {
                selHooks += '#easy_module-hook_' + values[i] + ',';
              }
              selHooks += '#easy_module-hook_' + values[n];
              $(selHooks).removeAttr('checked');
              values = [];
              updateSelectedHooks();
            }
          }
        };
      })();

      // Listen for (un)selections of hooks in the hooks table.
      $hooksTable.delegate('.easy-module_hook-check', 'change', function(evt){
        var $this = $(this);
        var hookId = $this.val();
        if($this.is(':checked')) {
          hooksSelect.tick(hookId);
        } else {
          hooksSelect.untick(hookId);
        }
      });

      // Listen for closing hooks from the selected hooks viewer.
      $('#easy_module_hooks_viewer').delegate('.choice-close', 'click', function(evt) {
        var hookId = $(this).html();
        hooksSelect.untick(hookId);
      });

      // Dialog for viewing hooks' sample code.
      var $hookCodeDlg = $('#hook_code_dlg').dialog({
        width: '660px',
        height: 450,
        autoOpen: false
      });

      // Listes for clicks for viewing hook's sample code.
      $hooksTable.delegate('.easy-module_hook_code', 'click', function(evt){
        evt.preventDefault();
        var $link = $(this);
        $.ajax({
          url: $link.attr('href'),
          success: function(data) {
            var $pre = $('<pre class="brush:php; gutter: false; tab-size: 2;">' + data + '</pre>');
            $hookCodeDlg.html($pre);
            SyntaxHighlighter.highlight($pre);
            $hookCodeDlg.dialog('option', 'title', $link.attr('title'))
            $hookCodeDlg.dialog('open');
          },
          error: function() {
            alert('Error while retrieving data');
          }
        });
      });
    }
  };

})(window, jQuery, Drupal);
