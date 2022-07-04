import React from 'react';
import Header from './Header';
import Content from './Content';
import Footer from './Footer';

class App extends React.Component {
	constructor(props) {
		super(props);
		
		const currentUrl = window.location.href;
		const urlNoQueryString = currentUrl.split("?")[0];
		const urlParts = urlNoQueryString.split("/")
		const fuseAction = urlParts.pop();
		const initialPath = urlParts.pop() + "/" + fuseAction;

		this.state = {
			path: initialPath
		}

		this.isDevelopment = (currentUrl.indexOf("localhost") > -1) ? true : false;
		this.urlAPIPrefix = this.isDevelopment ? "http://localhost:8080/sweetandsour" : "";
		this.updatePath = this.updatePath.bind();
	}

	updatePath(path) {
		this.setState({path: path});
	}

	render() {
		return (
			<>
				<a id="top"></a>
				<div className="page">
						<Header 
							path={this.state.path} 
							updatePath={(path) => this.updatePath(path)} 
							isDevelopment={this.isDevelopment}
							urlAPIPrefix={this.urlAPIPrefix}
						/>
						<Content 
							path={this.state.path}
							isDevelopment={this.isDevelopment}
							urlAPIPrefix={this.urlAPIPrefix}
						/>
						<Footer />
				</div>
			</>
		);
	}
}

export default App;
