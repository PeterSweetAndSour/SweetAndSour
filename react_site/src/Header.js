import React from 'react';
import Menu from './Menu';
import Spinner from './spinner';

class Header extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			path: this.props.path
		}
	}
	
	render() {
		return (
			<header>
				<p className="logo"><a href={this.homeUrl} title="Go to home page">Sweet and Sour</a></p>
				<p className="tagline">One is sweet and the other is &hellip; a web developer</p>
				<p className="sr-only"><a href="#content">Jump to content</a></p>{/* Hidden except for screen readers */}
				<form className="menu">
					<input className="menu" type="checkbox" id="menuToggle" />
					<label id="menuBtn" className="menu" htmlFor="menuToggle" role="button" aria-label="Toggle menu" aria-controls="imageMenu">Open</label>

					<Menu 
						path={this.state.path} 
						updatePath={(path) => this.updatePath(path)} 
						isDevelopment={this.props.isDevelopment}
						urlAPIPrefix={this.props.urlAPIPrefix}
					/>

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

export default Header