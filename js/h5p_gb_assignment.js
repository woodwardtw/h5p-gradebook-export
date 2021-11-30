let h5pDivs = document.querySelectorAll('.h5p-iframe-wrapper');

h5pDivs.forEach((h5pElement) => {
  let childId = h5pElement.children;
  h5pElement.id = childId[0].id+'-holder'
});
