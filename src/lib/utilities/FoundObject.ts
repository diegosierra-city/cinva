export default function foundObject(array:any[], campo:string, valor:any, view:string){
 if(!Array.isArray(array)) {
  return 'El parámetro array debe ser un arreglo';
}

if(array.length === 0) {
  // manejar caso de arreglo vacío 
  return 'El arreglo está vacío'; 
}
 ///busco en el array el objeto que coincida con el valor
 let resultado = array.find(element => element[campo] === valor);
 return resultado !== undefined ? resultado[view] : 'n/a';
} 