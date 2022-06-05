import React from 'react';
import Menu from './Menu';
import Spinner from './spinner';

class Header extends React.Component {
	render() {
		return (
			<header>
				<p className="logo"><a href={this.props.homeUrl} title="Go to home page">Sweet and Sour</a></p>
				<p className="tagline">One is sweet and the other is &hellip; a web developer</p>
				<p className="sr-only"><a href="#content">Jump to content</a></p>{/* Hidden except for screen readers */}
				<form className="menu">
					<input className="menu" type="checkbox" id="menuToggle" />
					<label id="menuBtn" className="menu" htmlFor="menuToggle" role="button" aria-label="Toggle menu" aria-controls="imageMenu">Open</label>

					<Menu homeUrl={this.props.homeUrl} fuseAction={this.props.fuseAction} />
					
					<div id="menuOverlay" className="menuOverlay">
						<div className="loading">
							<Spinner />
							<p>Just a moment &hellip;</p>
						</div>
					</div>
				</form>
			</header>	
		);
	}
}

/*
class Menu extends React.Component {
	createMarkup() {
		return {__html: this.props.menuHTML};
	}
	
	render() {
		return (
			<form className="menu">
				<input className="menu" type="checkbox" id="menuToggle" />
				<label id="menuBtn" className="menu" htmlFor="menuToggle" role="button" aria-label="Toggle menu" aria-controls="imageMenu">Open</label>
				<div dangerouslySetInnerHTML={this.createMarkup()} />
				<div id="menuOverlay" className="menuOverlay">
					<div className="loading">
						<Spinner />
						<p>Just a moment &hellip;</p>
					</div>
				</div>
			</form>
		);
	}
}
*/
export default Header