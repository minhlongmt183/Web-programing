package com.edisc.hinhhoc;

public class HinhChuNhat {
	static int count = 0;
	
	public static int chuVi(int chieuDai, int chieuRong) {
		return (chieuDai + chieuRong) * 2;
	}
	
	public static void main(String[] args) {
		
		// truy cap truc tiep bang class
		System.out.println(HinhChuNhat.count);
		
		HinhChuNhat.count += 5;
		System.out.println(HinhChuNhat.count);
		System.out.println(HinhChuNhat.chuVi(3, 4));
		
		// truy cap truc tiep bang doi tuong
		HinhChuNhat hinh1 = new HinhChuNhat();
		HinhChuNhat hinh2 = new HinhChuNhat();
		
		hinh1.count = 188;
		System.out.println(HinhChuNhat.count);
		
		hinh2.count = 500;
		System.out.println(HinhChuNhat.count);
		System.out.println(hinh1.count);
		
		System.out.println(hinh1.chuVi(18, 20));
		
		
	}

}
