var Setting = React.createClass({

	displayName : 'Setting',

	getInitialState()
	{
		return {
			title : ''
		};
	},

	componentDidMount()
	{
		this.setState({
			title : this.props.parent.refs.header.getTitle()
		});
	},

	updateFTP()
	{
		log('act update ftp');
		return false;
	},

	render()
	{
		return (
			<section>
				<h1>{this.state.title}</h1>
				<form method="post" name="ftpSetting" className="setting" onsubmit={this.updateFTP}>
					<fieldset>
						<legend className="blind">ftp 설정 폼</legend>
						<dl className="first">
							<dt><label htmlFor="host_name">Host name</label></dt>
							<dd><input type="text" name="host_name" id="host_name" maxLength="40" size="24" defaultValue="" placeholder="hostname.com"/></dd>
						</dl>
						<dl>
							<dt><label htmlFor="host_id">ID</label></dt>
							<dd><input type="text" name="host_id" id="host_id" size="15" maxLength="20" defaultValue="" placeholder="FTP ID"/></dd>
						</dl>
						<dl>
							<dt><label htmlFor="host_pw">Password</label></dt>
							<dd><input type="password" name="host_pw" id="host_pw" size="15" maxLength="20" defaultValue="" placeholder="FTP Password"/></dd>
						</dl>
						<dl>
							<dt><label htmlFor="host_pwd">Location</label></dt>
							<dd>
								<input type="text" name="host_pwd" id="host_pwd" size="40" maxLength="100" className="block" defaultValue={this.props.userData.ftp.pwd} placeholder="ftp Password"/>
								<p>goose가 설치된 ftp경로를 지정합니다.</p>
							</dd>
						</dl>
					</fieldset>
					<nav className="btn-group">
						<Link to="/" className="ui-button size-large">목록</Link>
						<button type="button" className="ui-button size-large color-key" data-action="test_ftp">FTP 테스트</button>
						<button type="submit" className="ui-button size-large color-key" data-action="update_ftp" disabled>업데이트</button>
					</nav>
				</form>
			</section>
		);
	}
});