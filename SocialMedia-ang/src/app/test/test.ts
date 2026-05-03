import { Component } from '@angular/core';
import { Server } from '../server';
@Component({
  selector: 'app-test',
  imports: [],
  standalone:true,
  templateUrl: './test.html',
  styleUrls: ['./test.css'],
})
export class Test {
  message: any;
  constructor(private server: Server) {
    console.log('test');
  }
  ngOnInit() {
    this.test();
  }
  test() {
    console.log('TEST');
    this.server.test().subscribe({
      next: (res: any) => {
        console.log('Success:', res);
        this.message = res.message;
      },
      error: (err) => {
        console.error('Error:', err);
        this.message = 'An error occurred while fetching data.';
      }
    });
  }
}
