(function( $ ) {

	/**
	 * Lo mismo que $.addClass pero dispara el evento "classAdded" y luego "classChanged" despues que la clase es añadida.
	 */
	$.fn.addClassPlus = function(className) {

		this.addClass(className);
		this.trigger('classAdded', { className : className, sender : this });
		this.trigger('classChanged', { className : className, sender : this });
		return this;
	};

	/**
	 * Lo mismo que $.removeClass pero dispara el evento "classRemoved" y luego "classChanged" despues que la clase es removida.
	 */
	 $.fn.removeClassPlus = function(className) {

		this.removeClass(className);
		 this.trigger('classRemoved', { className : className, sender : this });
		 this.trigger('classChanged', { className : className, sender : this });
		return this;
	};

	/**
	 * Converts <select>/<option> elements into <div> or any other kind of html elements.
	 *
	 * Important. Your <select> elements must have the attributes "id" and "name" and the <option> elements must contain the "value" attribute.
	 *
	 * Usage: This code is quite simple to use, check out this example.
	 *
	 * <code>
	 *     // Converts all the selects in the page into <div>'s.
	 *     SelectsToElements("select", null, function(originalSelect){
		 *          $(originalSelect).attr("hidden","");
		 *      });
	 * </code>
	 *
	 * <glossary>
	 *     dived: Whenever I say "dived", I mean the converted version of a <select> or <option> element, into divs or any other html element (span, ul, li, etc).
	 *     folded: The "folded" element is the div (or other element) that displays the currently selected option. // TODO: Rename this word to preview or selected.
	 * </glossary>
	 *
	 * @param settings customize how to convert the <select> elements into other type of elements.
	 * @param whatToDoWithOriginalSelect callback that is passed the original <select> DOM element when the conversion finishes. Useful if you want, for instance, hide, the original element.
	 * @constructor
	 */
	$.fn.SelectsToElements = function(settings, whatToDoWithOriginalSelect)
	{
		if(typeof(settings) == 'undefined' || settings == false || settings == null) settings = {};

		// TODO: Add support for element <optgroup>.
		// TODO: Listen to the events "addClass" and "removeClass" of the original elements, so when their clases are modified, the dived version's classes get updated too. I think it there might be more jquery events we should be listening to.
		// TODO: Add keyboard (arrow keys) functionality. Down/Enter to fold out the widget, SpaceBar/Enter to select a value.
		// TODO: If a dived <select> is open, and we click another one, the prviously opened should be closed.
		// TODO: In the dived <select>, save a "data-" with the id of the original <select>.
		// TODO: Add support to store the "data-"'s and the rest of attributes of the <select>/<option>'s.
		// TODO: If a click on a disabled dived <option> was made, the foldout toggle must not work.
		// TODO: This must work backwardly, that is, when the actual original <select> is updated, the value of the "folded" element must be updated. I should subscribe to the original <select> events, but don't rely on them, because the original ones might be deleted by the user in the callback of the function. Also, this widget must provide an event API, with only the "onchange" event. NOTE: Don't worry about this as long as you don't delete the original <select> elements, just hide them.
		// TODO: Add a class for the currently selected dived <option> elements, so they can be highlighted whenever the widget is folded out.

		return this.each(function(i, value)
		{
			// ----- SETTINGS START

			// TODO: Make it possible to receive an args variable where to customize these settings.

			var masterPrefix = !!settings.masterPrefix ? settings.masterPrefix : "dived";
			var idPrefixForDivedSelect = masterPrefix + "-";
			var commonClassForDivedSelect = masterPrefix + "-select";
			var commonClassForDivedOption = masterPrefix + "-option";
			var divedElementForSelect = "div";
			var divedElementForOption = "div";
			var conserveOriginalSelectClasses = true; // Nota: Debería ser siempre true para preservar las clases de los <select>.

			/**
			 * If true, the dived <select>'s container (the whole widget) will be placed after the original <select>. Else, it will be placed before it.
			 */
			var putAfter = true;

			/**
			 * Class to put to the container element of the dived <select> when the widget is folded down, if this class is not present, the widget is folded up.
			 * @type {string}
			 */
			var foldedDownClass = "folded-down";

			var selectedOptionClass = masterPrefix + "-option-selected";

			// ----- SETTINGS END

			// Get the <select> element.

			var $originalSelect = $(value);

			// Get/generate the id of the <select> element.

			var originalSelectId = $originalSelect.attr("id");
			if(typeof(originalSelectId) == 'undefined' || originalSelectId == false)
			{
				console.log("Warning: The <select name='" + $originalSelect.attr('name') + "'> element has not an id. You must specify an unique id for all the <select> elements you want to convert.");
				// If the <select> has not an id, generate one random id.
				originalSelectId = Math.random().toString(36).substring(2).replace(" ", "-");
			}

			// Get the class of the <select> element.

			var originalSelectClass = $originalSelect.attr("class");
			if(typeof(originalSelectClass) == 'undefined' || originalSelectClass == false) originalSelectClass = "";

			// Id for the dived <select>.

			var divedSelectId = idPrefixForDivedSelect + originalSelectId;

			// Create the alternative <select>.

			var $divedSelect = $originalSelect.parent().append(
				'<' + divedElementForSelect + ' ' +
				'id="' + divedSelectId + '"' +
				'class="' + commonClassForDivedSelect + ' "' +
				'data-name="' + $originalSelect.attr("name") + '"' +
					// TODO: Maybe we need to loop throughout all the attributes of the <select> with $.attrs().
				'></' + divedElementForSelect + '>');

			// Extract the <element> from the the newly generated code. Note that <element> might be any html element, like a div, span, etc.
			// TODO: This code is not strong enough because it requires the original <select>'s to have an unique "id", otherwise, this code might work unexpectedly.
			$divedSelect = $divedSelect.find("#" + divedSelectId);

			// Create the alternative <option>'s.

			var selectedOptionText = "";
			var selectedOptionIndex = -1;

			$originalSelect.children('option').each(function(optionIndex)
			{
				// Get the class attribute of the <option> element.
				var originalOptionClass = $(this).attr("class");
				if(typeof(originalOptionClass) == 'undefined' || originalOptionClass == false) originalOptionClass = "";

				var isTheSelectedOption = optionIndex == 0 || this.selected;

				if(isTheSelectedOption)
				{
					selectedOptionText = $(this).html();
					selectedOptionIndex = optionIndex;
				}

				$divedSelect.append(
					'<'+divedElementForOption+' ' +
					'data-value="' + this.value + '"' +
					'class="' + originalOptionClass + " " + commonClassForDivedOption + '"' +
					(this.selected ? "data-selected " : "") + // Note: Don't use just the word "selected" because it might not work well in all browsers since not all the html elements have as valid that attribute.
					(this.disabled ? "data-disabled " : "") + // Note: Don't use just the word "disabled" because it might not work well in all browsers since not all the html elements have as valid that attribute.
					'>'+$(this).html()+'</'+divedElementForOption+'>');
			});

			// Get the <option> elements.

			var $divedOptions = $divedSelect.find("." + commonClassForDivedOption);

			// Add a class to the dived selected <option> so it can be highlighted.

			if($divedOptions.length >= 1) $($divedOptions.get(selectedOptionIndex)).addClass(selectedOptionClass);

			// Move the dived <select> and <option>'s into a container.

			var containerId = divedSelectId + "-ctn";
			var containerClass = masterPrefix + "-select-ctn";
			if(conserveOriginalSelectClasses)
			{
				containerClass += " " + originalSelectClass;
			}

			var containerHtml = "<div id='" + containerId + "' class='" + containerClass + "'></div>";
			$divedSelect.append(containerHtml);
			var $container = $divedSelect.find("#" + containerId).first();
			$container.parent().parent().append($container);
			$container.append($divedSelect);

			// Create a "folded" element. That will be used to show the dived <select> when it's not folded out.

			var foldedId = divedSelectId + "-folded";
			var foldedHtml = "<div id='"+foldedId+"' class='" + masterPrefix + "-select-folded'>"+selectedOptionText+"</div>"; // TODO: Get what element is the currently default. It't the first one in the options, or the last one with the selected attribute on it.
			$container.prepend(foldedHtml);
			var $folded = $container.find("#" + foldedId);

			if(putAfter)
			{
				$originalSelect.after($container);
			}
			else
			{
				$originalSelect.before($container);
			}
			/*
			 var masterContainerHtml = "<div id='"+foldedId+"-master' class='grupo-select'></div>";
			 $container.after(masterContainerHtml);
			 var $masterContainer = $container.parent().find("#" + foldedId + "-master");
			 $masterContainer.parent().parent().append($masterContainer);
			 $masterContainer.prepend($originalSelect)
			 */

			/**
			 * Will be called whenever the widget is clicked.
			 */
			function toggleIt()
			{
				$("." + foldedDownClass+":not(#"+$container.attr("id")+")").removeClass(foldedDownClass);
				$container.toggleClass(foldedDownClass);
			}

			// When a "folded" element is clicked, the dived <select> will toggle.

			$container.click(toggleIt);

			// A click anywhere, will fold up all the dived <select>'s as long as the click was not made on a folded up element.

			$(window).click(function(e){
				var didClickInside = GetRect($container).IsInside(e.clientX, e.clientY);
				//if(!didClickInside) $container.removeClass(foldedDownClass); // TODO: Esta linea debe estar descomentada, pero en la pagina de comunidad-bien-para-bien, da problemas.
			});

			// When a dived <option> is clicked (OnOptionClicked).

			$divedOptions.click(function()
			{
				var $option = $(this);

				var isDisabled = $option.attr("data-disabled");

				if(typeof(isDisabled) != 'undefined' || isDisabled == false)
				{
					//console.log($option.html() + " is disabled.");
					return;
				}

				// A click on a dived <option> will update the value of the "folded" element.

				$folded.html($option.html());

				// Update the "selected" class in the dived <options>.
				$divedOptions.removeClass(selectedOptionClass);
				$option.addClass(selectedOptionClass);

				// Set the value of the clicked element to the original <select> and trigger the "change" event on the original <select>.

				$originalSelect.val($option.data("value")).trigger("change");
			});

			// When the original <select> gets modified, update this widget properly.

			$originalSelect.change(function () {
				var $changedDivedOption = $divedSelect.find("[data-value='"+this.value+"']");
				$folded.html($changedDivedOption.html());
				$divedOptions.removeClass(selectedOptionClass);
				$changedDivedOption.addClass(selectedOptionClass);
			});

			// When the original <select> is added/removed a class, that class will be also added/removed into the dived version.

			$originalSelect.bind('classAdded', 'none', function(event, data){
				$folded.addClass(data.className);
			});

			$originalSelect.bind('classRemoved', 'none', function(event, data){
				$folded.removeClass(data.className);
			});

			// If a value for whatToDoWithOriginalSelect was defined, call that callback and pass the original <select> as parameter.

			if(typeof(whatToDoWithOriginalSelect) != 'undefined' && typeof(whatToDoWithOriginalSelect) != false)
			{
				whatToDoWithOriginalSelect(value);
			}

			if( settings.hasOwnProperty("CtnClass") )
			{
				$container.addClass(settings.CtnClass);
			}

			$originalSelect.one("DOMSubtreeModified", function () {
				/* Cuando select-live cambie, habrá que
				 - Eliminar toodo el <dived> container generado para ese select.
				 - Volver a generar el <dived> para ese select.
				 */
				$container.remove();
				SelectsToElements($originalSelect, settings, whatToDoWithOriginalSelect);
			});
		});
	}
}( jQuery ));

/**
 * Representa un rectángulo con valores para top, left, bottom y right.
 */
function Rect()
{
	this.top = 0;
	this.left = 0;
	this.bottom = 0;
	this.right = 0;

	/**
	 * Regresa true si las coordenadas dadas por los 'x' y 'y' están adentro del rectángulo.
	 * @return bool
	 */
	this.IsInside = function(x, y)
	{
		return !this.IsOutside(x, y);
	};

	/**
	 * Regresa true si las coordenadas dadas por los 'x' y 'y' están afuera del rectángulo.
	 * @return bool
	 */
	this.IsOutside = function(x, y)
	{
		return (x < this.left || x > this.right || y > this.bottom || y < this.top);
	};
}

/**
 * Consigue un objeto Rect que contiene las coordenadas globales de un elemento html.
 *
 * Uso:
 * <code>
 * 		var didClickInside = GetRect(htmlElement).IsInside(e.clientX, e.clientY);
 * </code>
 */
function GetRect(element)
{
	var $container = $(element);
	var offset = $container.offset();
	var rect = new Rect();
	rect.top = offset.top;
	rect.left = offset.left;
	rect.right = $container.width() + rect.left;
	rect.bottom = $container.height() + rect.top;
	return rect;
}
