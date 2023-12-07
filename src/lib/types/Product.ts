export interface Product {
 id: number
 category_id: number
 proveedor_id: number
 product: string
 ref: string
 description: string
 description2: string
 price: number
 size: string
 color: string
 unidad: string
 stock:number
 image1: string
 image2: string
 image3: string
 image4: string
 position: number
 options: string
 home: boolean
 active: boolean
 variants: Array<any>
}