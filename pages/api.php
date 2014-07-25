<section class="Documents">
	<div class="hgroup">
		<h1>Goose API 안내</h1>
	</div>

	<section class="first">
		<h1>개요</h1>
		<p>
			외부 프로그램에서 좀 더 쉽게 Goose DB에 접근할 수 있는 방법을 고민하다가 json이나 xml데이터를 제공하는 api프로그램을 만들게 되었습니다. 이 api를 활용하여 쉽게 데이터를 조회하고 사용할 수 있을거라는 기대감을 가지고 있습니다.<br/>
			api 주소로 이동하면 주소 파라메터 값을 근거하여 데이터를 검색하고 출력합니다. 여기에서 출력한 데이터로 활용하여 자신의 서비스를 개발 할 수 있습니다.<br/>
			예를들어 iOS프로그램에서 Goose프로그램의 데이터를 얻어서 iOS프로그램에 출력하고 데이터를 조작할 수 있을 것입니다.
		</p>
	</section>

	<section>
		<h1>동작과정</h1>
		<p>Goose API는 다음과 같은 과정으로 작동합니다.</p>
		<ol>
			<li>API Key 인증</li>
			<li>파라메터값을 조합하여 DB에서 데이터 검색</li>
			<li>검색한 데이터(php배열)로 각각의 데이터(json,xml)의 형태로 변환 및 출력</li>
			<li>출력된 페이지 내용으로 지 꼴리는대로 사용</li>
		</ol>
	</section>

	<section>
		<h1>API Key</h1>
		<p>
			apikey값은 /{goose}/data/config/user.php 파일의 $api_key 값을 암호화한 아래의 값을 사용하여 인증합니다.
			<input type="text" value="<?=md5($api_key)?>" class="block" readonly />
		</p>
	</section>

	<section>
		<h1>Parameter</h1>
		<p>
			Goose api는 url주소형식으로 요청합니다. 주소를 적는 방식은 다음과 같습니다. 아래 주소는 가장 기초적인 형태이며 조건에 따라 아래 설명된 규칙을 참고하여 url을 만드시면 됩니다.<br/>
			<strong>http://xxx.com/goose/api/item?apikey=XXX&table=nests&act=index&output=json</strong><br/>
		</p>
		<table class="ui-table">
			<caption>필수 파라메터 (Required parameters). 요청시 꼭 넣어줘야하는 값입니다.</caption>
			<thead>
				<tr>
					<th scope="col" width="12%">키</th>
					<th scope="col" width="12%">타입</th>
					<th scope="col">설명</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="nowrap">apikey</th>
					<td class="nowrap center">string</td>
					<td>인증에 필요한 값입니다. 이전섹션에(API Key) 안내되어 있습니다.</td>
				</tr>
				<tr>
					<th class="nowrap">table</th>
					<td class="nowrap center">string</td>
					<td>
						Database 테이블 이름입니다.<br/>
						자세한 내용과 테이블 값을 확인하려면 <a href="<?=GOOSE_ROOT?>/help/#HelpDatabase" target="_blank">이곳</a>을 참고하시길 바랍니다.
					</td>
				</tr>
				<tr>
					<th class="nowrap">act</th>
					<td class="nowrap center">string</td>
					<td>
						데이터를 하나만 출력할것인지 복수로 출력할것인지 정하는 값입니다.<br/>
						act 값에 따라 필요로 하는 파라메터값이 변하기 때문에 필요한 값을 참고하려면 아래 도표를 참고해주세요.<br/>
						값 : index|single
					</td>
				</tr>
				<tr>
					<th class="nowrap">output</th>
					<td class="nowrap center">string</td>
					<td>
						데이터 출력방식을 정합니다. 값이 없으면 'html'로 출력됩니다.<br/>
						값 : json|xml|html
					</td>
				</tr>
			</tbody>
		</table>
		<table class="ui-table">
			<caption>"&act=index" 값으로 정할때 테이블에 따라 사용되는 파라메터</caption>
			<thead>
				<tr>
					<th scope="col" width="12%">테이블</th>
					<th scope="col" width="12%">키</th>
					<th scope="col" width="12%">타입</th>
					<th scope="col">설명</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="nowrap" rowspan="5">articles</th>
					<td class="nowrap center">srl</td>
					<td class="nowrap center">number</td>
					<td>article 고유번호</td>
				</tr>
				<tr>
					<td class="nowrap center">group_srl</td>
					<td class="nowrap center">number</td>
					<td>둥지 그룹번호</td>
				</tr>
				<tr>
					<td class="nowrap center">nest_srl</td>
					<td class="nowrap center">number</td>
					<td>둥지 고유번호</td>
				</tr>
				<tr>
					<td class="nowrap center">category_srl</td>
					<td class="nowrap center">number</td>
					<td>분류 고유번호</td>
				</tr>
				<tr>
					<td class="nowrap center">thumnail_srl</td>
					<td class="nowrap center">number</td>
					<td>썸네일 이미지가 되는 files 테이블 srl값</td>
				</tr>

				<tr>
					<th class="nowrap" rowspan="2">categories</th>
					<td class="nowrap center">srl</td>
					<td class="nowrap center">number</td>
					<td>분류 고유번호</td>
				</tr>
				<tr>
					<td class="nowrap center">nest_srl</td>
					<td class="nowrap center">number</td>
					<td>둥지 고유번호</td>
				</tr>

				<tr>
					<th class="nowrap" rowspan="2">files</th>
					<td class="nowrap center">srl</td>
					<td class="nowrap center">number</td>
					<td>첨부파일 고유번호</td>
				</tr>
				<tr>
					<td class="nowrap center">article_srl</td>
					<td class="nowrap center">number</td>
					<td>article 고유번호</td>
				</tr>

				<tr>
					<th class="nowrap">nestGroups</th>
					<td class="nowrap center">srl</td>
					<td class="nowrap center">number</td>
					<td>둥지그룹 고유번호</td>
				</tr>

				<tr>
					<th class="nowrap" rowspan="3">nests</th>
					<td class="nowrap center">srl</td>
					<td class="nowrap center">number</td>
					<td>둥지 고유번호</td>
				</tr>
				<tr>
					<td class="nowrap center">group_srl</td>
					<td class="nowrap center">number</td>
					<td>둥지 그룹번호</td>
				</tr>
				<tr>
					<td class="nowrap center">id</td>
					<td class="nowrap center">string</td>
					<td>둥지 ID값</td>
				</tr>

				<tr>
					<th class="nowrap">jsons</th>
					<td class="nowrap center">srl</td>
					<td class="nowrap center">number</td>
					<td>jsons 고유번호</td>
				</tr>
			</tbody>
		</table>
		<table class="ui-table">
			<caption>"&act=index" 값으로 정할때 테이블 구분없이 공통으로 사용되는 파라메터</caption>
			<thead>
				<tr>
					<th scope="col" width="12%">키</th>
					<th scope="col" width="12%">타입</th>
					<th scope="col">설명</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="nowrap">order</th>
					<td class="nowrap center">string</td>
					<td>정렬기준 필드</td>
				</tr>
				<tr>
					<th class="nowrap">sort</th>
					<td class="nowrap center">string</td>
					<td>
						오름차순, 내림차순<br/>
						값) asc|desc
					</td>
				</tr>
				<tr>
					<th class="nowrap">limit</th>
					<td class="nowrap center">number</td>
					<td>출력갯수</td>
				</tr>
				<tr>
					<th class="nowrap">page</th>
					<td class="nowrap center">number</td>
					<td>페이지 번호</td>
				</tr>
				<tr>
					<th class="nowrap">search_key</th>
					<td class="nowrap center">string</td>
					<td>키워드 검색 필드</td>
				</tr>
				<tr>
					<th class="nowrap">search_value</th>
					<td class="nowrap center">string</td>
					<td>
						키워드 검색 값. 키워드 검색필드값과 같이 필요합니다.
						<pre style="margin:3px 0 0">search_key like '%search_value%'</pre>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="ui-table">
			<caption>"&act=single" 값으로 정할때 필요한 파라메터</caption>
			<thead>
				<tr>
					<th scope="col" width="12%">키</th>
					<th scope="col" width="12%">타입</th>
					<th scope="col">설명</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="nowrap">field</th>
					<td class="nowrap center">string</td>
					<td>
						출력할 테이블 필드를 지정합니다.<br/>
						예) srl,title,regdate
					</td>
				</tr>
				<tr>
					<th class="nowrap">key</th>
					<td class="nowrap center">string</td>
					<td>필드이름</td>
				</tr>
				<tr>
					<th class="nowrap">value</th>
					<td class="nowrap center">string<br/>number</td>
					<td>key값에 대한 값. <em>(key=value)</em></td>
				</tr>
				<tr>
					<th class="nowrap">search_key</th>
					<td class="nowrap center">string</td>
					<td>키워드 검색 필드</td>
				</tr>
				<tr>
					<th class="nowrap">search_value</th>
					<td class="nowrap center">string</td>
					<td>
						키워드 검색 값. 키워드 검색필드값과 같이 필요합니다.
						<pre style="margin:3px 0 0">search_key like '%search_value%'</pre>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
</section>
