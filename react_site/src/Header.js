import React from 'react';
import Spinner from './spinner';
import SweetAndSour from './sweetandsour';

class Header extends React.Component {
	constructor(props) {
		super(props);

    this.state = {
      error: null,
      menuAPICalled: false,
      menuHTML: {},
			homeUrl: props.homeUrl
    };
	}

	componentDidMount() {
		if(!this.state.menuAPICalled) {
			const urlMenuAPI = "http://localhost:8080/sweetandsour/api/get_menu.php?fuseAction=" + this.props.fuseAction;
			fetch(urlMenuAPI)
			.then(response => response.json())
			.then(
				(data) => {
					this.setState({menuHTML: data.menuHTML}, this.menuAPIHasReturned);
				},
				// Note: it's important to handle errors here instead of a catch() block
				// so that we don't swallow exceptions from actual bugs in components.
				(error) => {
					this.setState({
						menuHTML: "",
						error: error
					});
				}
			)
		}
  }

	menuAPIHasReturned() {
		SweetAndSour.initialize();
		this.setState({menuAPICalled: true});
	}

	render() {
    if (this.state.error) {
      return <div>Error: {this.state.error.message}</div>;
    } 
		else if (!this.state.menuAPICalled) {
      return <div>Loading...</div>;
    } 
		else {
      return (
				<header>
					<p className="logo"><a href={this.props.homeUrl} title="Go to home page">Sweet and Sour</a></p>
					<p className="tagline">One is sweet and the other is &hellip; a web developer</p>
					<p className="sr-only"><a href="#content">Jump to content</a></p>{/* Hidden except for screen readers */}
					<Menu menuHTML={this.state.menuHTML} />
				</header>	
      );
    }
  }
}

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

export default Header