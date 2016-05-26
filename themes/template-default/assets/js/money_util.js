(function(){
	/**
	 * Formats a number to represent a currency formatted value.
	 * @param c int How many decimals.
	 * @param d string Thousand separator. Default: ".".
	 * @param t string Decimals separator. Default: ",".
	 * @return string The formatted number.
	 */
	Number.prototype.formatMoney = function(c, d, t){
		var n = this,
			c = isNaN(c = Math.abs(c)) ? 2 : c,
			d = d == undefined ? "." : d,
			t = t == undefined ? "," : t,
			s = n < 0 ? "-" : "",
			i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
			j = (j = i.length) > 3 ? j % 3 : 0;
		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};

	/**
	 * Extracts a fload number from a string. The only characters that are allowed beside the numbers (0-9), are the "," and ".". Where the "," must be used to separate thousands, and the "." to indicate decimals.
	 * @return float. The parsed number.
	 */
	String.prototype.extractNumber = function(){
		return parseFloat(this.replace(/[^0-9\.]/g,''));
	};
})();
