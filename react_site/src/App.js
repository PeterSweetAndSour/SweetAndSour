import React from 'react';
//import UseScript from './useScript';
import SweetAndSour from './sweetandsour';

class App extends React.Component {

	render() {
		const currentUrl = window.location.href;
		const urlNoQueryString = currentUrl.split("?")[0];
		const fuseAction = urlNoQueryString.split("/").pop();
		const path = urlNoQueryString.split("/").pop() + "/" + fuseAction;

		return (
			<>
				<a id="top"></a>
				<div className="page">
						<Header path={path} />
						<Content fuseAction={fuseAction} />
						<Footer />
				</div>
			</>
		);
	}
}

class Header extends React.Component {
	constructor(props) {
		super(props);

    this.state = {
      error: null,
      menuAPICalled: false,
      menuHTML: {}
    };
	}

	componentDidMount() {
		if(!this.state.menuAPICalled) {
			const urlMenuAPI = "http://localhost:8080/sweetandsour/api/get_menu.php?path=" + this.props.path;
			fetch(urlMenuAPI)
			.then(response => response.json())
			.then(
				(data) => {
					this.setState({menuHTML: data.menuHTML}, this.menuAPIHasBeenCalled);
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

	menuAPIHasBeenCalled() {
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
					<p className="logo"><a href="<?= $homeUrl ?>home" title="Go to home page">Sweet and Sour</a></p>
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
				<div dangerouslySetInnerHTML = {this.createMarkup()} />
				<div id="menuOverlay" className="menuOverlay"><div className="loading"><p><img src="<?=$rootRelativeUrl ?>images/loading_20080830.gif" alt="Just a moment ..." />Just a moment &hellip;</p></div></div>
			</form>
		);
	}
}

class Content extends React.Component {
	render() {
		return (
			<main id="content">
				stuff goes here
			</main>
		);
	}
}

class Footer extends React.Component {
	render() {
		const thisYear = new Date().getFullYear();
		// Can't use HTML entitities like &copy; in React :-(
		const copyright = thisYear + String.fromCharCode(169) + " This page was last updated sometime before you viewed it.";

		return (
			<footer>
				{/* Link to top of page. */}
				<p><a className="toTop" href='#top'>To top</a></p>

				{/*  Copyright notice. */}
				<p className="copyright">{copyright}</p>
			</footer>
		);
	}
}

export default App;
