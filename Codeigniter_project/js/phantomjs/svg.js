var page = new WebPage;
page.viewportSize = { width: 990, height : 350 };

print(getArgumentLenth());

/*
for(x in phantom.args) {
  print(x + ' : ' + phantom.args[x]);
}
*/

print(getArgument(0));
print(getArgument(1));

//page.content = '<svg width="900" height="500"><defs id="defs"><clipPath id="_ABSTRACT_RENDERER_ID_0"><rect x="161" y="96" width="579" height="309"/></clipPath></defs><rect x="0" y="0" width="900" height="500" stroke="none" stroke-width="0" fill="#ffffff"/><g><text text-anchor="start" x="161" y="70.9" font-family="Arial" font-size="14" font-weight="bold" stroke="none" stroke-width="0" fill="#000000">Company Performance</text></g><g><rect x="754" y="96" width="132" height="37" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><rect x="754" y="96" width="132" height="14" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="773" y="107.9" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#222222">Sales</text></g><rect x="754" y="96" width="14" height="14" stroke="none" stroke-width="0" fill="#3366cc"/></g><g><rect x="754" y="119" width="132" height="14" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="773" y="130.9" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#222222">Expenses</text></g><rect x="754" y="119" width="14" height="14" stroke="none" stroke-width="0" fill="#dc3912"/></g></g><g><rect x="161" y="96" width="579" height="309" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g clip-path="url(#_ABSTRACT_RENDERER_ID_0)"><g><rect x="161" y="404" width="579" height="1" stroke="none" stroke-width="0" fill="#cccccc"/><rect x="161" y="327" width="579" height="1" stroke="none" stroke-width="0" fill="#cccccc"/><rect x="161" y="250" width="579" height="1" stroke="none" stroke-width="0" fill="#cccccc"/><rect x="161" y="173" width="579" height="1" stroke="none" stroke-width="0" fill="#cccccc"/><rect x="161" y="96" width="579" height="1" stroke="none" stroke-width="0" fill="#cccccc"/></g><g><rect x="189" y="174" width="44" height="230" stroke="none" stroke-width="0" fill="#3366cc"/><rect x="333" y="130" width="44" height="274" stroke="none" stroke-width="0" fill="#3366cc"/><rect x="478" y="261" width="44" height="143" stroke="none" stroke-width="0" fill="#3366cc"/><rect x="622" y="166" width="44" height="238" stroke="none" stroke-width="0" fill="#3366cc"/><rect x="234" y="328" width="44" height="76" stroke="none" stroke-width="0" fill="#dc3912"/><rect x="378" y="313" width="44" height="91" stroke="none" stroke-width="0" fill="#dc3912"/><rect x="523" y="143" width="44" height="261" stroke="none" stroke-width="0" fill="#dc3912"/><rect x="667" y="292" width="44" height="112" stroke="none" stroke-width="0" fill="#dc3912"/></g><g><rect x="161" y="404" width="579" height="1" stroke="none" stroke-width="0" fill="#333333"/></g></g><g/><g><g><text text-anchor="middle" x="233.75" y="425.9" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#222222">2004</text></g><g><text text-anchor="middle" x="378.25" y="425.9" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#222222">2005</text></g><g><text text-anchor="middle" x="522.75" y="425.9" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#222222">2006</text></g><g><text text-anchor="middle" x="667.25" y="425.9" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#222222">2007</text></g><g><text text-anchor="end" x="147" y="409.4" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#444444">100</text></g><g><text text-anchor="end" x="147" y="332.4" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#444444">400</text></g><g><text text-anchor="end" x="147" y="255.4" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#444444">700</text></g><g><text text-anchor="end" x="147" y="178.4" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#444444">1,000</text></g><g><text text-anchor="end" x="147" y="101.4" font-family="Arial" font-size="14" stroke="none" stroke-width="0" fill="#444444">1,300</text></g></g></g><g><g><text text-anchor="middle" x="450.5" y="468.9" font-family="Arial" font-size="14" font-style="italic" stroke="none" stroke-width="0" fill="#ff0000">Year</text></g></g><g/></svg>';
page.content = getArgument(1);
setTimeout(function(){
  page.render(getArgument(0));
  phantom.exit();
}, 200)

function print(str) {
  console.log(str);
}
function getArgument(index) {
  return phantom.args[index];
}

function getArgumentLenth() {
  return phantom.args.length;
}