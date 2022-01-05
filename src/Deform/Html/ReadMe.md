# Html

Simple chainable system for generating an HTML DOM structure which can be cast to a string.

Unfortunately DOMElement objects are immutable otherwise we'd be able to bake them in directly. Consequently the
- converted all or in part into DOMDocument/DOMElement tree, allowing late manipulation using css/xpath selectors, and 
  from there into a string)
- directly converted into a string 

Notes:
- a basic selector system is provided for searching and adjusting nodes, currently this only supports:
  * tag name : e.g. 'div'
  * tag class : e.g. '.div-class'
  * tag id : e.g. '#div-id'
