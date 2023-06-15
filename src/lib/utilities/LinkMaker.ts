
export const linkMaker = (str: string) => {
  let result = str.replace(/ /g, "-")
  return result.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

export const linkMakerUndo = (str: string) => {
  let result = str.replace(/_/g, " ")
  return result.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

