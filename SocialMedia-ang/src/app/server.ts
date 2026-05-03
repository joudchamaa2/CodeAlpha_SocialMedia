import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
@Injectable({
  providedIn: 'root',
})
export class Server {
  constructor(private http : HttpClient) {}
    test(){
    return this.http.get<any>('http://127.0.0.1:8000/api/test',{
      headers: {
        'Accept': 'application/json',
      }
    })
  }
}