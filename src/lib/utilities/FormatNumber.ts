export const formattedNumber = (n: number) => {
 let locale: string = "es-CO";
 return n.toLocaleString(locale)
 //return '*'+n
};
