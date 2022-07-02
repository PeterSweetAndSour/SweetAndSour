import React from 'react';

class MenuItem extends React.Component {
	render() {
		const hasChildren = typeof this.props.children !== "undefined" ? true : false;
		const inputName = "menuLevel" + this.props.menuLevel;
		const inputID = "radio" + this.props.menuID;
		const listClass = "menu" + (this.props.menuLevel + 1);

		const isMenuLevel1 = this.props.menuLevel === 1 ? true : false;
		const anchorClass = isMenuLevel1 ? "photoLink" : "";

		return (
			<li className={this.props.classList}>
				<a className={anchorClass} href={this.props.linkUrl}>
					{isMenuLevel1 ? (
						<>
							<span></span>
							<p className="verticalText">{this.props.displayText}</p>
						</>
					) : (
						this.props.displayText
					)}
				</a>

				{hasChildren && (
					<>
					<input type="radio" name={inputName} id={inputID}></input>
					<label htmlFor={inputID}>{this.props.displayText}</label>
					<ul className={listClass}>
						{this.props.children.map((menuItemData) => {
							return <MenuItem 
												key={menuItemData.menu_id} 
												menuID={menuItemData.menu_id} 
												classList={menuItemData.classList}
												linkUrl={menuItemData.linkUrl}
												displayText={menuItemData.display_text}
												menuLevel={menuItemData.menu_level}
												children={menuItemData.children}
											/>
						})}	
					</ul>
					</>
				)}
			</li>
		)
	}
}

export default MenuItem;

/*
<MenuItem 
											key={menuItemData.menu_id} 
											classList={menuItemData.classList}
											linkUrl={menuItemData.linkUrl}
											displayText={menuItemData.display_text}
											children={menuItemData.children}
										/>
*/