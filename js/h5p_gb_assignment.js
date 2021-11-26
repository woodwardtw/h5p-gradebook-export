let h5pDivs = document.querySelectorAll('.h5p-iframe-wrapper');

h5pDivs.forEach((h5pElement) => {
  let childId = h5pElement.children;
  console.log(childId[0].id)
  h5pElement.id = childId[0].id+'-holder'
});