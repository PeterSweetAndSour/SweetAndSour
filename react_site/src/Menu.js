import React from 'react';
import SweetAndSour from './sweetandsour';

// Recreating in React what was done server-side in act_constructMenu.php

class Menu extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			error: null,
			menuAPICalled: false,
			menuHTML: {},
			menuData: [],
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
					this.setState({
						menuHTML: data.menuHTML,
						menuData: data.menuData
					}, 
					this.menuAPIHasReturned);
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

	createMarkup() {
		return {__html: this.state.menuHTML};
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
				<div dangerouslySetInnerHTML={this.createMarkup()} />
			);
		}

	}
}

export default Menu;