(function ($) {
  const data = window.fballerGeoAdminData || {};
  const termMap = data.termMap || {};
  const cityRoots = data.cityRoots || {};
  const rootTermIds = data.rootTermIds || {};

  function getCheckedTermId(taxonomy) {
    const $input = $("#" + taxonomy + "div input[type='checkbox']:checked").first();
    if (!$input.length) {
      return 0;
    }

    return parseInt($input.val(), 10) || 0;
  }

  function getTermMeta(taxonomy, termId) {
    if (!taxonomy || !termId || !termMap[taxonomy]) {
      return null;
    }

    return termMap[taxonomy][termId] || null;
  }

  function updateMetaboxVisibility(taxonomy, visibleCount) {
    const $wrapper = $("#" + taxonomy + "div").closest(".postbox");
    if (!$wrapper.length) {
      return;
    }

    $wrapper.toggle(visibleCount > 0);
  }

  function setRootChecklistItemVisibility($item, visible) {
    $item.children("label, .selectit").toggle(visible);
    $item.children("input[type='checkbox']").toggle(visible);
    $item.children("ul.children").show();
  }

  function toggleChecklistTerms() {
    const selectedCity = getCheckedTermId("city");
    const selectedDirection = getCheckedTermId("city_direction");
    const selectedAdminArea = getCheckedTermId("admin_area");
    const selectedDistrict = getCheckedTermId("district");

    ["city_direction", "admin_area", "district", "metro"].forEach(function (taxonomy) {
      const $box = $("#" + taxonomy + "div");
      if (!$box.length) {
        return;
      }

      let visibleCount = 0;

      $box.find("li").each(function () {
        const $item = $(this);
        const $input = $item.find("input[type='checkbox']").first();
        const termId = parseInt($input.val(), 10) || 0;
        const meta = getTermMeta(taxonomy, termId);

        if (!meta) {
          return;
        }

        let visible = true;

        if (meta.isRoot) {
          visible = false;
        }

        if (visible && selectedCity && meta.city && meta.city !== selectedCity) {
          visible = false;
        }

        if (visible && taxonomy === "admin_area" && selectedDirection && meta.direction && meta.direction !== selectedDirection) {
          visible = false;
        }

        if (visible && taxonomy === "district") {
          if (selectedAdminArea && meta.adminArea && meta.adminArea !== selectedAdminArea) {
            visible = false;
          } else if (!selectedAdminArea && selectedDirection && meta.direction && meta.direction !== selectedDirection) {
            visible = false;
          }
        }

        if (visible && taxonomy === "metro") {
          if (selectedDistrict && meta.district && meta.district !== selectedDistrict) {
            visible = false;
          } else if (!selectedDistrict && selectedAdminArea && meta.adminArea && meta.adminArea !== selectedAdminArea) {
            visible = false;
          } else if (!selectedDistrict && !selectedAdminArea && selectedDirection && meta.direction && meta.direction !== selectedDirection) {
            visible = false;
          }
        }

        if (!visible && $input.is(":checked")) {
          $input.prop("checked", false);
        }

        if (meta.isRoot) {
          setRootChecklistItemVisibility($item, false);
        } else {
          $item.toggle(visible);
        }

        if (visible) {
          visibleCount += 1;
        }
      });

      updateMetaboxVisibility(taxonomy, visibleCount);
    });
  }

  function filterSelectOptions() {
    const selectedCity = parseInt($("#fballer_related_city").val(), 10) || 0;
    const selectedDirection = parseInt($("#fballer_related_direction").val(), 10) || 0;
    const selectedAdminArea = parseInt($("#fballer_related_admin_area").val(), 10) || 0;

    [
      { selector: "#fballer_related_direction", taxonomy: "city_direction" },
      { selector: "#fballer_related_admin_area", taxonomy: "admin_area" },
      { selector: "#fballer_related_district", taxonomy: "district" }
    ].forEach(function (config) {
      const $select = $(config.selector);
      if (!$select.length) {
        return;
      }

      let hasVisibleOptions = false;

      $select.find("option").each(function () {
        const $option = $(this);
        const termId = parseInt($option.val(), 10) || 0;
        const meta = getTermMeta(config.taxonomy, termId);

        if (!termId || !meta) {
          $option.prop("hidden", false);
          return;
        }

        let visible = !meta.isRoot;

        if (visible && selectedCity && meta.city && meta.city !== selectedCity) {
          visible = false;
        }

        if (visible && config.taxonomy === "admin_area" && selectedDirection && meta.direction && meta.direction !== selectedDirection) {
          visible = false;
        }

        if (visible && config.taxonomy === "district") {
          if (selectedAdminArea && meta.adminArea && meta.adminArea !== selectedAdminArea) {
            visible = false;
          } else if (!selectedAdminArea && selectedDirection && meta.direction && meta.direction !== selectedDirection) {
            visible = false;
          }
        }

        $option.prop("hidden", !visible);
        if (visible) {
          hasVisibleOptions = true;
        }
      });

      const currentValue = parseInt($select.val(), 10) || 0;
      const currentOptionHidden = currentValue ? $select.find("option[value='" + currentValue + "']").prop("hidden") : false;
      if (currentOptionHidden) {
        $select.val("");
      }

      const $field = $select.closest(".form-field");
      if ($field.length) {
        $field.toggle(hasVisibleOptions || ["city_direction", "admin_area"].includes(config.taxonomy));
      }

      if (["city_direction", "admin_area"].includes(config.taxonomy) && !selectedCity) {
        $field.show();
      }
      if (config.taxonomy === "district" && !selectedCity) {
        $field.show();
      }
    });
  }

  function cascadeChecklistRefresh() {
    toggleChecklistTerms();
  }

  function syncParentWithCity() {
    const selectedCity = parseInt($("#fballer_related_city").val(), 10) || 0;
    const taxonomy = data.screenTaxonomy;
    const roots = cityRoots[taxonomy] || {};
    const rootTermId = roots[selectedCity] || 0;
    const $parent = $("#parent");

    if ($parent.length && rootTermId) {
      $parent.val(rootTermId);
    }
  }

  function hideRootRowsOnTermScreens() {
    const taxonomy = data.screenTaxonomy;
    const rootIds = rootTermIds[taxonomy] || [];

    if (!taxonomy || !rootIds.length || !["edit-tags", "term"].includes(data.screenBase)) {
      return;
    }

    rootIds.forEach(function (termId) {
      $("#tag-" + termId).hide();
    });
  }

  $(document).on("change", "#citydiv input[type='checkbox'], #city_directiondiv input[type='checkbox'], #admin_areadiv input[type='checkbox'], #districtdiv input[type='checkbox']", cascadeChecklistRefresh);
  $(document).on("change", "#fballer_related_city, #fballer_related_direction, #fballer_related_admin_area", function () {
    filterSelectOptions();
    syncParentWithCity();
  });

  $(document).ready(function () {
    cascadeChecklistRefresh();
    filterSelectOptions();
    syncParentWithCity();
    hideRootRowsOnTermScreens();
  });
})(jQuery);
