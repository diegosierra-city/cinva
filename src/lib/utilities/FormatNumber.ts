export const formattedNumber = (n: number) => {
 let locale: string = "es-CO";
 const rounded = Math.ceil(n);
 return rounded.toLocaleString(locale)
};
