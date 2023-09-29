export interface User {
    id: number
    nombre: string
    documento:string
	email: string
    tipo: string
    permisos: string
	user_time_life: number
    token: string
    activo: boolean
}

export interface UserControl {
    id: number
    nombre: string
    documento:string
	email: string
    tipo: string
    permisos: string
	activo: boolean
}