import MyController from "./MyController";

const sound = 'data:audio/mpeg;base64,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';
let asked = false;
export default class extends MyController {
	static values = {
		soundUrl: String,
		id: String
	};

	initialize() {
		if (asked) {
			return;
		}
		asked = true;
		this.audio = new Audio(sound);
		this.audio.volume = 0.1;
		let promise = this.audio.play();
		this.askForPermisions(promise);

	}

	askForPermisions(promise) {
		if (promise !== undefined) {
			promise.then(_ => {
			}).catch(error => {
				console.log(error)
				// Autoplay was prevented.
				let div = document.createElement('div');
				div.innerHTML = 'Please click <button class="btn btn-info btn-sm">here</button> to enable sound';
				div.addEventListener('click', () => {
					this.audio.play();
					this.audio.addEventListener('ended', () => {
						this.audio = null;
					})
					div.closest('.toast').remove();
				});
				this.showToast('Warning', div)
					.then();
			});
		}
	}

	idValueChanged() {
		if (!this.soundUrlValue) {
			return;
		}
		if (!this.audio) {
			this.audio = new Audio(this.soundUrlValue);
		}
		this.audio.volume = 1;
		let promise = this.audio.play().then();
		this.askForPermisions(promise);
	}
}
