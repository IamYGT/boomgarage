;(function(API) {
'use strict'
API.myText = function(txt, options, x, y) {
			options = options ||{};
			if( options.align == "center" ){
				// Get current font size
				var fontSize = this.internal.getFontSize();
	
				// Get page width
				var pageWidth = this.internal.pageSize.width;
	
				var txtWidth = this.getStringUnitWidth(txt)*fontSize/this.internal.scaleFactor;
	
				// Calculate text's x coordinate
				x = ( pageWidth - txtWidth ) / 2;
			} else if(options.align == "right"){				
				// Get current font size
				var fontSize = this.internal.getFontSize();
	
				// Get page width
				var pageWidth = this.internal.pageSize.width;
				
				txtWidth = this.getStringUnitWidth(txt)*fontSize/this.internal.scaleFactor;
				
				// Calculate text's x coordinate
				x = (typeof x != "undefined" ? (( pageWidth - txtWidth ) - x) : ( pageWidth - txtWidth ) );
			}
	
			// Draw text at x,y
			this.text(txt,x,y);
		}

})(jsPDF.API);

// jsPDF addon
	(function(API){
		
	})(jsPDF.API);
