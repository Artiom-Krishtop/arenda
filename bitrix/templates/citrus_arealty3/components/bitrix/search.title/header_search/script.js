var citrusLiveSearch = function(arParams){
    var _this = this;
    this.arParams = {
        'AJAX_PAGE': arParams.AJAX_PAGE,
        'CONTAINER_ID': arParams.CONTAINER_ID,
        'INPUT_ID': arParams.INPUT_ID,
        'MIN_QUERY_LEN': parseInt(arParams.MIN_QUERY_LEN),
        'DELAY': parseInt(arParams.DELAY)
    };
    this.$input = $("#"+this.arParams.INPUT_ID);
    this.$container = $("#"+this.arParams.CONTAINER_ID);
    this.$form = this.$container.find(".js-search");
    this.$overlay = $(".js-search-overlay-hide");
    this.$result_list = this.$container.find(".js-search-result-list");
    this.CASH = {};
    this.RESULT = [];
    this.loadingCount = 0;
    this.timeoutId = false;

    this.checkLength = function () {
        return this.$input.val().length >= this.arParams.MIN_QUERY_LEN;
    };
    this.updateFormClass = function(cnt) {
        var cnt = cnt || 0;
        var val = _this.$input.val();
        this.loadingCount += cnt;

        //loading
        this.$form[this.loadingCount ? "addClass" : "removeClass" ]("is-loading");

        //cancel or search
        if(val.length > 0 ) {
            _this.$form.addClass("is-cancel").removeClass("is-empty");
        } else {
            _this.$form.addClass("is-empty").removeClass("is-cancel");
        }
    };
    this.toggleDescriptionText = function (){
        if(_this.checkLength()) {
            _this.$form.find(".search-result-description").addClass("hidden");
            _this.$form.find(".js-search-result-no")[ this.RESULT.length ? "addClass" : "removeClass"]("hidden");
        } else {
            _this.$form.find(".search-result-description").removeClass("hidden");
            _this.$form.find(".js-search-result-no").addClass("hidden");
        }
    };
    this.clearResult = function(){
        this.RESULT = [];
        this.updateResultView();
    };
    this.clear = function(){
        this.clearResult();
        this.$input.val('');
    };

    this.updateResultView = function (){
        this.$result_list.html("");
        _this.toggleDescriptionText();
        _this.RESULT.forEach(function (item, index) {
            var selectedCLass = !index ? "_selected" : "";
            htmlItem = '<a href="'+item.URL+'" class="search-result-item ' + selectedCLass + ' ">';
            if(item.PICTURE)
                htmlItem += '<div class="search-result-item-images">' +
                                '<span style="background-image: url(\''+item.PICTURE+'\')"></span>'+
                            '</div>';

            htmlItem += '<div class="search-result-item-body">';
                htmlItem += '<div class="search-result-item-name">'+item.NAME+'</div>';
                if(item.ADDRESS)
                    htmlItem += '<div class="search-result-item-address">'+item.ADDRESS+'</div>';
                if(item.COST)
                    htmlItem += '<div class="search-result-item-price">'+item.COST+'</div>';
            htmlItem += '</div>';
            htmlItem += '</a>';
            _this.$result_list.append(htmlItem);
        });
    };
    this.updateResult = function(val){
        if(!val) return;
        this.selectIndex = 0;
        if(_this.timeoutId) clearTimeout(_this.timeoutId);

        if( _this.CASH[val] ) {
            _this.RESULT = _this.CASH[val];
            _this.updateResultView();
            return;
        }
        _this.timeoutId = setTimeout(function(){
            _this.updateFormClass(1);
            $.ajax({
                url: _this.arParams.AJAX_PAGE,
                type: 'POST',
                dataType: 'json',
                data: {
                    'ajax_call': 'y',
                    'INPUT_ID': _this.arParams.INPUT_ID,
                    'q': val,
                    'l': _this.arParams.MIN_QUERY_LEN
                }
            })
            .done(function(result) {
                var result = result || [];
                _this.RESULT = result["ITEMS"];
                _this.CASH[result["QUERY"]] = result["ITEMS"];
                _this.updateResultView();
                _this.updateFormClass(-1);
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {

            });
        }, _this.arParams.DELAY);
    };

    this.selectIndex = 0;
    this.selectResultItem = function(pos, exactly){
        if(typeof pos === "undefined" || !this.RESULT.length) return;
        var newIndex = !!exactly ? pos : this.selectIndex + pos;
        newIndex =  newIndex > this.RESULT.length-1 ? this.RESULT.length-1 :
                    newIndex < 0 ? 0 :
                    newIndex;
        if( this.selectIndex === newIndex ) return;

        this.selectIndex = newIndex;
        this.$result_list.find(".search-result-item._selected").removeClass("_selected");
        this.$result_list.find(".search-result-item").eq(newIndex).addClass("_selected");
    };
    this.init = function () {
        //для проверки на изменение слова
        var input_val = "";
        var keyCode = {
            38: "ArrowUp",
            40: "ArrowDown",
            9: "Tab",
            13: "Enter",
            27: "Escape"
        };
        var processInput = function(val) {
            if (_this.checkLength() ) {
                if(val !== input_val) {
                    _this.updateResult(val);
                }
            } else {
                if(_this.timeoutId) clearTimeout(_this.timeoutId);
                _this.clearResult();
            }
            input_val = val;
            _this.updateFormClass();
        };
        this.$input
                .on("keydown", function(event){
                    var eventName = keyCode[event.keyCode];
                    if(eventName == "ArrowUp" || eventName == "ArrowDown") {
                        event.preventDefault();
                        var changeIndex = eventName == "ArrowUp" ? -1 : eventName == "ArrowDown" ? 1 : 0;
                        _this.selectResultItem(changeIndex);
                    }
                })
                .on("keyup", function(event){
                    if(keyCode[event.keyCode]) {
                        event.preventDefault();
                        return;
                    }
                    processInput($(this).val());
                })
                .on("focusin click", function(event){
                    $('html').addClass('open-search');
                });

        processInput(this.$input.val());
        if (this.$input.is(':focus')) {
            $('html').addClass('open-search');
        }

        this.$form.on("click", ".js-search-cancel", function (event) {
            event.preventDefault();
            _this.clear();
            _this.toggleDescriptionText();
            _this.updateFormClass();
            $('html').removeClass('open-search');
            _this.$input.blur();
        });
        this.$overlay
                .on("click", function(){
                    $('html').removeClass('open-search');
                });
        $(document).on('keyup', function (event) {
            //escape
            if( keyCode[event.keyCode] == "Escape" && $('html').hasClass('open-search') ) {
                $('html').removeClass('open-search');
                _this.$input.blur();
            }
        });
        this.$result_list
            .on("touchstart", function(){
                _this.$input.blur();
            })
            .on("mouseover", ".search-result-item", function () {
                _this.selectResultItem($(this).index(), true);
            });
        //submit
        _this.$form.on("submit", function(event){
            event.preventDefault();
            if( _this.RESULT && _this.RESULT[_this.selectIndex]) {
                document.location.href = _this.RESULT[_this.selectIndex]["URL"];
            }
        });
    };
    this.init();
};
