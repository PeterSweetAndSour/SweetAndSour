import React from 'react';
import MenuItem from './MenuItem';
import SweetAndSour from './sweetandsour';

class Menu extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			path: this.props.path,
			menuAPICalled: false,
			heirarchicalMenuDataArray: null
		};

		this.menuData = {};
		this.flatMenuDataArray = [];
		this.selectedID = null;
		this.parentID = null;
		this.grandparentID = null;

		this.errorMsg = null;
		
		this.fuseAction = this.props.path.split("/")[1];
		this.isDevelopment = this.props.isDevelopment;
		this.urlAPIPrefix = this.props.urlAPIPrefix;
		this.homeUrl = this.isDevelopment ? "/sweetandsour/" : "/";

		this.handleMenuClick = this.handleMenuClick.bind();
	}

	componentDidMount() {
		if(!this.state.menuAPICalled) {
			const urlMenuAPI = this.urlAPIPrefix + "/api/get_menu.php?fuseAction=" + this.fuseAction;
			fetch(urlMenuAPI)
			.then(response => response.json())
			.then(
				(data) => {
					this.selectedID = data.selectedID;
					this.parentID = data.parentID;
					this.grandparentID = data.grandparentID;
					this.menuData = data.menuData;
					this.menuAPIHasReturned();
				},
				// Note: it's important to handle errors here instead of a catch() block
				// so that we don't swallow exceptions from actual bugs in components.
				(error) => {
					this.errorMsg = "<p>Unable to load menu. Sorry.</p>" + error + "</p>";
				}
			)
		}
	}

	menuAPIHasReturned() {
		this.setFlatMenuDataArray();
		this.setClassList();
		this.setPathAndLinkUrls();
		this.setState({menuAPICalled: true});
		this.setHeirarchicalMenuDataArray();
		SweetAndSour.initialize();
	}

	setFlatMenuDataArray() {
		// Make a proper array from the menuData property of the object
		this.flatMenuDataArray = Object.keys(this.menuData).map((key) => {
			return this.menuData[key] ;
		});
	}

	setClassList() {
		let menuLevel = null;
		let indexLastLevel2MenuItem = null;
		let menuItemData = null;

		for(var i=0; i<this.flatMenuDataArray.length; i++) {
			menuItemData = this.flatMenuDataArray[i];
			menuItemData.classList = [];
			menuLevel = menuItemData.menu_level;

			if(menuLevel === 1) {
				menuItemData.classList.push(menuItemData.display_text.replace(/ /g, "").toLowerCase());
			}

			// Mark all the selected menu items
			if(this.menuItemIsSelected(i)) {
				menuItemData.classList.push("selected");

				// The second level menu item before the one that is selected needs a special class
				if(menuLevel === 2 & indexLastLevel2MenuItem !== null) {
					this.flatMenuDataArray[indexLastLevel2MenuItem].classList += " beforeSelected";
					indexLastLevel2MenuItem = null; // Reset
				}				
			}
			else if(menuLevel === 2) {
				indexLastLevel2MenuItem = i;
			}

			// Look ahead to see if the menu level is increasing as we need to add a class on this list item
			if(i < this.flatMenuDataArray.length - 1 && (this.flatMenuDataArray[i+1].menu_level > menuLevel)) {
				menuItemData.classList.push("hasChildren");
			}

			menuItemData.classList = menuItemData.classList.join(" ");
		}
	}

	setPathAndLinkUrls() { /* Urls won't actually be used since will be calling event.preventDefault */
		let menuItemData = null;
		for(var i=0; i<this.flatMenuDataArray.length; i++) {
			menuItemData = this.flatMenuDataArray[i];
			const path = menuItemData.folder_name + "/" + menuItemData.fuse_action;
			menuItemData.path = path;
			menuItemData.linkUrl = this.homeUrl + path;;
		}
	}

	menuItemIsSelected(i) {
		const menuItemID = this.flatMenuDataArray[i].menu_id
		if(menuItemID === this.selectedID
			|| menuItemID === this.parentID
			|| menuItemID === this.grandparentID) {
				return true;
		}
		else {
			return false;
		}
	}

	getItemOrAncestor(tree, positionStack, levelsUp) {
		var selectedItem = tree;
		for(var i=0; i<positionStack.length - levelsUp; i++) {
			selectedItem = selectedItem.children[positionStack[i]];
		}
		return selectedItem;
	}

	setHeirarchicalMenuDataArray() {
		let menuItemData = null;
		let currentMenuLevel = null;
		let lastMenuLevel = 1;
		let tree = {};
		tree.children = [];
		let positionStack = []; // Array will have position of current item, so Insta is [0,1].
		                        // and Newsletters, letter 4, page 2 will be [3, 4, 1] which
														// will translate to tree[3][4][1].
		let lastItem = null;
		let parent = null;
		let ancestor = null;
		let levelsUp = 0;
		let lastPosition = null;

		for(var i=0; i<this.flatMenuDataArray.length; i++) {
			menuItemData = this.flatMenuDataArray[i];
			currentMenuLevel = menuItemData.menu_level;

			if(currentMenuLevel > lastMenuLevel) {    // First child of last item
				lastItem = this.getItemOrAncestor(tree, positionStack, 0);
				lastItem.children = [menuItemData];

				// Push 0 on to stack
				positionStack.push(0);
			}
			else if(currentMenuLevel === lastMenuLevel) { // Sibling of previous menu item
				// Find parent of last element so we can push on current element
				parent = this.getItemOrAncestor(tree, positionStack, 1);
				parent.children.push(menuItemData)

				// Increment item in stack
				lastPosition = positionStack.pop();
				positionStack.push(typeof (lastPosition) !== "undefined"  ? lastPosition + 1 : 0);
			}
			else { // Up at least one level from previous menu item
				levelsUp = lastMenuLevel - currentMenuLevel;

				// Pop off that many items from stack
				for(var j=0; j<levelsUp; j++) {
					positionStack.pop();
				}

				// Find ancestor
				ancestor = this.getItemOrAncestor(tree, positionStack, levelsUp) 

				// Push item onto children array of ancestor
				ancestor.children.push(menuItemData);

				// Increment item in stack
				lastPosition = positionStack.pop();
				positionStack.push(lastPosition + 1);
			}

			lastMenuLevel = currentMenuLevel;
		}

		this.setState({heirarchicalMenuDataArray: tree.children}); 
	}

	handleMenuClick(event) {
		event.preventDefault();

		const target = event.target;
		const path = target.dataset.path;
		this.props.updatePath(path);

		//Remove "selected" class from currently selected list items. Automatic?

		//Add "selected" class to target and any ancestors. Automatic?

		//Change the visible URL by updating history
		const newUrl = this.homeUrl + path;
		window.history.pushState({}, '', newUrl);
  }

	render() {
		if(!this.state.menuAPICalled) {
			return (
				<div>Loading...</div>
			);
		}

		if(this.state.error) {
			return (
				this.state.errorMsg
			);
		}

		return (
			<nav id="imageMenu" aria-hidden="true" aria-labelledby="menuBtn" role="navigation">
				<ul className="menu1">
					{this.state.heirarchicalMenuDataArray.map((menuItemData) => {
						return <MenuItem 
											key={menuItemData.menu_id} 
											menuID={menuItemData.menu_id} 
											classList={menuItemData.classList}
											path={menuItemData.path}
											linkUrl={menuItemData.linkUrl}
											displayText={menuItemData.display_text}
											menuLevel={menuItemData.menu_level}
											children={menuItemData.children}
											updatePath={(path) => this.updatePath(path)} 
											onClick={function(e) {this.handleMenuClick(e);}}
										/>
					})}	
				</ul>
			</nav>
		);
	}
}

export default Menu;