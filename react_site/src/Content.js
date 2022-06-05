import React from 'react';

class Content extends React.Component {
	constructor(props) {
		super(props);

    this.state = {
      error: null,
      contentAPICalled: false,
			showLinkToTop: true,
      contentHTML: {}
    };
	}

	componentDidMount() {
		if(!this.state.contentAPICalled) {
			const urlContentAPI = "http://localhost:8080/sweetandsour/api/get_content.php?path=" + this.props.path;
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
  }

	contentAPIHasReturned() {
		this.setState({contentAPIHasBeenCalled: true});
	}
	
	createMarkup() {
		console.log("setting contentHTML");
		return {__html: this.state.contentHTML};
	}

	render() {
		return (
			<main id="content" dangerouslySetInnerHTML={this.createMarkup()} />
		);
	}
}

export default Content;