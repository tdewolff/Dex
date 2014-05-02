var Save = function (root) {
	var self = this;

	this.root = $(root);
	this.hasChange = false;
	this.timeout = null;

	this.needsSave = function () {
		self.hasChange = true;
		clearTimeout(self.timeout);
		self.timeout = setTimeout(self.save, 1000);
	};

	this.save = function () {
		self.hasChange = false;
		clearTimeout(self.timeout);
		self.root.trigger({
			type: 'save'
		});
	};

	this.root.on('input', function (e) {
		self.needsSave();
	});

	this.root.on('change', function (e) {
		if (self.hasChange || e.target.tagName.toLowerCase() === 'select') {
			self.save();
		}
	});

	$(document).on('keydown', function (e) {
		if (e.ctrlKey && e.which === 83) {
			e.preventDefault();
			self.save();
			return false;
		}
	});
};