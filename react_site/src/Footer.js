import React from 'react';

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

export default Footer;