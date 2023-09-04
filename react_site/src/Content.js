import React from 'react';

class Content extends React.Component {
	constructor(props) {
		super(props);

	  this.state = {
			path: this.props.path,
			error: null,
			contentHTML: {},
			contentAPICalled: false,
			showLinkToTop: true,
		};

		// WHERE DO URLREWRITE RULES GO IN REACT?
		this.isDevelopment = this.props.isDevelopment;
		this.urlAPIPrefix = this.props.urlAPIPrefix;
		this.homeUrl = this.isDevelopment ? "/sweetandsour/" : "/";
	}

	getContent() {
		const urlContentAPI = this.urlAPIPrefix + "/api/get_content.php?path=" + this.state.path;
		fetch(urlContentAPI)
		.then(response => response.json())
		.then(
				(data) => {
						this.setState({
								contentHTML: data.contentHTML,
								showLinkToTop: (data.showLinkToTop === 1) ? true : false
						},
						this.contentAPIHasReturned);
				},
				// Note: it's important to handle errors here instead of a catch() block
				// so that we don't swallow exceptions from actual bugs in components.
				(error) => {
						this.setState({
								contentHTML: "",
								error: error
						});
				}
		)
  }

	componentDidMount() {
		this.getContent();
  }


	render() {
		if(this.state.error) {
			return (
				<p className="error">Unable to retrieve content from database. Sorry.</p>
			);
		}

		console.log("TypeOf contentHTML: " + typeof (this.state.contentHTML));
		console.log("TypeOf path: " + typeof (this.state.path));
		return (
			<main id="content">{this.state.contentHTML}</main> 
		);
	}
}

export default Content;