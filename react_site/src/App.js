import React from 'react';
import Header from './Header';
import Content from './Content';
import Footer from './Footer';

class App extends React.Component {

	render() {
		const currentUrl = window.location.href;
		const urlNoQueryString = currentUrl.split("?")[0];
		const urlParts = urlNoQueryString.split("/")
		const fuseAction = urlParts.pop();
		const path = urlParts.pop() + "/" + fuseAction;

		// WHERE DO URLREWRITE RULES GO IN REACT?
		const homeUrl = (currentUrl.indexOf("localhost") > -1) ? "/sweetandsour/home/welcome" : "/home/welcome";

		return (
			<>
				<a id="top"></a>
				<div className="page">
						<Header fuseAction={fuseAction} homeUrl={homeUrl} />
						<Content path={path} />
						<Footer />
				</div>
			</>
		);
	}
}

export default App;
