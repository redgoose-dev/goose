var Setting = React.createClass({

	displayName : 'Setting',
	$form : null,

	getInitialState()
	{
		return {
			title : '',
			readyFTP : false
		};
	},

	componentDidMount()
	{
		this.$form = $(ReactDOM.findDOMNode(this.refs.settingForm));
		this.setState({
			title : this.props.parent.refs.header.getTitle()
		});
	},

	// check FTP
	checkFTP()
	{
		let self = this;

		$.post(this.props.userData.url + '/testFTP/', this.$form.serialize(), function(response){
			response = JSON.parse(response);
			if (response.state == 'success')
			{
				self.setState({ readyFTP : true });
				alert(response.message);
			}
			else
			{
				self.setState({ readyFTP : false });
				alert(response.message);
			}
		});

	},

	render()
	{
		return (
			<section>
				<h1>{this.state.title}</h1>
				<form
					action={this.props.userData.url + '/updateFTP/'}
					method="post"
					name="ftpSetting"
					className="setting"
					ref="settingForm">
					<input
						type="hidden"
						name="redir"
						defaultValue={this.props.userData.url + '/#/setting/'}/>
					<fieldset>
						<legend className="blind">ftp 설정 폼</legend>
						<dl className="first">
							<dt><label htmlFor="host_name">Host name</label></dt>
							<dd>
								<input
									type="text"
									name="host_name"
									id="host_name"
									maxLength="40"
									size="24"
									defaultValue={(this.props.userData.ftp.host) ? this.props.userData.ftp.host : ''}
									placeholder="hostname.com"/>
							</dd>
						</dl>
						<dl>
							<dt><label htmlFor="host_id">ID</label></dt>
							<dd>
								<input
									type="text"
									name="host_id"
									id="host_id"
									size="15"
									maxLength="20"
									defaultValue={(this.props.userData.ftp.id) ? this.props.userData.ftp.id : ''}
									placeholder="FTP ID"/>
							</dd>
						</dl>
						<dl>
							<dt><label htmlFor="host_pw">Password</label></dt>
							<dd><input type="password" name="host_pw" id="host_pw" size="15" maxLength="20" defaultValue="" placeholder="FTP Password"/></dd>
						</dl>
						<dl>
							<dt><label htmlFor="host_pwd">Location</label></dt>
							<dd>
								<input
									type="text"
									name="host_pwd"
									id="host_pwd"
									size="40"
									maxLength="100"
									className="block"
									defaultValue={this.props.userData.ftp.pwd}
									placeholder="ftp Password"/>
								<p>goose가 설치된 ftp경로를 지정합니다.</p>
							</dd>
						</dl>
					</fieldset>
					<nav className="btn-group">
						<Link to="/" className="ui-button size-large">목록</Link>
						<button type="button" className="ui-button size-large color-key" onClick={this.checkFTP}>FTP 테스트</button>
						<button type="submit" className="ui-button size-large color-key" data-action="update_ftp" disabled={(this.state.readyFTP) ? '' : 'disabled'}>업데이트</button>
					</nav>
				</form>
			</section>
		);
	}
});